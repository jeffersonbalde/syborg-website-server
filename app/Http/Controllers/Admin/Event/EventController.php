<?php

namespace App\Http\Controllers\Admin\Event;

use App\Http\Controllers\Controller;
use App\Models\Events;
use App\Models\StudentUser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    /**
     * Display a listing of events with attendance stats
     */
    public function index(Request $request)
    {
        $query = Events::query();

        // Search filter
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                  ->orWhere('location', 'like', '%'.$request->search.'%');
            });
        }

        // Date filter
        if ($request->has('date')) {
            $query->whereDate('event_date', $request->date);
        }

        // Get pagination parameters
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);

        $events = $query->withCount([
                'attendances as present_students_count' => function ($query) {
                    $query->where('present', true);
                },
                'attendances as absent_students_count' => function ($query) {
                    $query->where('present', false);
                }
            ])
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $events->items(),
            'meta' => [
                'total' => $events->total(),
                'per_page' => $events->perPage(),
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
            ]
        ]);
    }

    /**
     * Get attendees for a specific event
     */
    public function getAttendees(Events $event)
    {
        $attendees = $event->attendances()
            ->with('student')
            ->where('present', true)
            ->get()
            ->map(function ($attendance) {
                return [
                    'edp_number' => $attendance->student->edp_number,
                    'fullname' => $attendance->student->firstname . ' ' .
                                  $attendance->student->middlename . ' ' .
                                  $attendance->student->lastname,
                    'course' => $attendance->student->course,
                    'year_level' => $attendance->student->year_level,
                    'time_in' => $attendance->time_in,
                    'time_out' => $attendance->time_out
                ];
            });

        return response()->json(['data' => $attendees]);
    }

    /**
     * Get absent students for a specific event
     */
    public function getAbsentStudents(Events $event)
    {
        // Get all students
        $allStudents = StudentUser::all();

        // Get attending student EDP numbers
        $attendingEdpNumbers = $event->attendances()
            ->where('present', true)
            ->pluck('edp_number');

        // Filter absent students
        $absentStudents = $allStudents->reject(function ($student) use ($attendingEdpNumbers) {
            return $attendingEdpNumbers->contains($student->edp_number);
        })->values();

        return response()->json([
            'data' => $absentStudents->map(function ($student) {
                return [
                    'edp_number' => $student->edp_number,
                    'fullname' => $student->firstname . ' ' .
                                 $student->middlename . ' ' .
                                 $student->lastname,
                    'course' => $student->course,
                    'year_level' => $student->year_level,
                    'contact_number' => $student->contact_number
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateEventData($request);

        $event = Events::create($validatedData);

        return response()->json([
            'message' => 'Event created successfully.',
            'data' => $event
        ], 201);
    }

    protected function validateEventData(Request $request, Events $event = null)
    {
        return $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tbl_Events')->ignore($event ? $event->id : null)
            ],
            'location' => 'nullable|string',
            'event_date' => [
            'required',
            'date',
            function ($attribute, $value, $fail) {
                $year = date('Y', strtotime($value));
                $currentYear = date('Y');

                if ($year < $currentYear) {
                    $fail('The event year cannot be in the past.');
                }

                if ($year > $currentYear + 5) {
                    $fail('The event year is too far in the future.');
                }
            },
        ],

            'start_time' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (strtotime($value) < strtotime('today')) {
                        $fail('The start time must be today or in the future.');
                    }
                }
            ],
            'end_time' => 'required|date|after:start_time',
        ], [
        'end_time.after' => 'The end time must be after the start time'
    ]);
    }
}
