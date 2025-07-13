<?php

namespace App\Http\Controllers\Admin\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Events;
use App\Models\Attendance;
use App\Models\StudentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    /**
     * Get event details for attendance
     */
    public function getEventDetails(Events $event)
    {
        return response()->json([
            'event' => $event,
            'attendees' => $event->attendances()
                ->with('student')
                ->orderBy('edp_number')
                ->orderBy('time_in')
                ->get()
                ->map(function ($attendance) {
                    return $this->formatAttendance($attendance);
                })
        ]);
    }

    /**
     * Format attendance response
     */
    private function formatAttendance($attendance)
    {
        return [
            'id' => $attendance->id,
            'edp_number' => $attendance->edp_number,
            'fullname' => $attendance->student->firstname . ' ' .
                          $attendance->student->middlename . ' ' .
                          $attendance->student->lastname,
            'course' => $attendance->student->course,
            'year_level' => $attendance->student->year_level,
            'time_in' => $attendance->time_in,
            'time_out' => $attendance->time_out,
            'present' => (bool)$attendance->present // Ensure boolean value
        ];
    }

    /**
    * Manual attendance by EDP number
    */
    public function manualAttendance(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:tbl_Events,id',
            'edp_number' => 'required|exists:tbl_StudentUser,edp_number',
            'type' => 'required|in:time_in,time_out',
            'excuse_reason' => 'nullable|string',
            'excuse_file' => 'nullable|file|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $student = StudentUser::where('edp_number', $request->edp_number)->firstOrFail();

            // Handle file upload
            $filePath = null;
            if ($request->hasFile('excuse_file')) {
                $filePath = $request->file('excuse_file')->store('excuses');
            }

            $existing = Attendance::where('event_id', $request->event_id)
                ->where('edp_number', $request->edp_number)
                ->lockForUpdate() // Prevent concurrent updates
                ->first();

            // Time-in validation
            if ($request->type === 'time_in') {
                if ($existing && $existing->time_in) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Student already has a Time In record for this event',
                        'attendance' => $this->formatAttendance($existing->load('student'))
                    ], 400);
                }
            }

            // Time-out validation
            if ($request->type === 'time_out') {
                if (!$existing || !$existing->time_in) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Student has no Time In record for this event',
                    ], 400);
                }

                if ($existing->time_out) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Student already has a Time Out record for this event',
                        'attendance' => $this->formatAttendance($existing->load('student'))
                    ], 400);
                }
            }

            $attendanceData = [
                $request->type => now()->setTimezone('Asia/Manila'), // Explicit timezone
                'excuse_reason' => $request->excuse_reason ?? ($existing ? $existing->excuse_reason : null),
                'excuse_file' => $filePath ?? ($existing ? $existing->excuse_file : null),
            ];

            // Only update present status when recording time-out
            if ($request->type === 'time_out') {
                $attendanceData['present'] = true;
            }

            $attendance = Attendance::updateOrCreate(
                [
                    'event_id' => $request->event_id,
                    'edp_number' => $request->edp_number
                ],
                $attendanceData
            );

            DB::commit();
            return response()->json([
                'message' => 'Attendance recorded successfully',
                'attendance' => $this->formatAttendance($attendance->load('student'))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error recording attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    private function recordAttendance($eventId, $edpNumber)
    {
        $attendance = Attendance::updateOrCreate(
            [
                'event_id' => $eventId,
                'edp_number' => $edpNumber // Now using ID instead of EDP number
            ],
            [
                'present' => true,
                'time_in' => now(),
                'time_out' => null
            ]
        );

        return response()->json([
            'message' => 'Attendance recorded successfully',
            'attendance' => $this->formatAttendance($attendance->load('student'))
        ]);
    }

    public function saveBulkAttendance(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:tbl_Events,id',
            'attendees' => 'required|array',
            'attendees.*.edp_number' => 'required|exists:tbl_StudentUser,edp_number'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->attendees as $attendee) {
                Attendance::updateOrCreate(
                    [
                        'event_id' => $request->event_id,
                        'edp_number' => $attendee['edp_number']
                    ],
                    [
                        'present' => true,
                        'time_in' => now(),
                        'time_out' => null
                    ]
                );
            }
            DB::commit();
            return response()->json(['message' => 'Attendance saved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error saving attendance'], 500);
        }
    }

    public function scanAttendance(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:tbl_Events,id',
            'qr_code' => 'required|string'
        ]);

        $student = StudentUser::where('qr_code', $request->qr_code)->first();

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        return $this->recordAttendance($request->event_id, $student->edp_number);
    }


    /**
     * Remove an attendance record
     */
    public function removeAttendance(Attendance $attendance)
    {
        $attendance->delete();
        return response()->json(['message' => 'Attendance removed successfully']);
    }

}
