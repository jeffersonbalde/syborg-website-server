<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentProfilePicture;
use App\Models\StudentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentApprovedMail;
use App\Mail\StudentDisapprovedMail;   

use Intervention\Image\Facades\Image;

class StudentController extends Controller
{
   public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'edp_number'     => 'required|unique:tbl_StudentUser,edp_number',
            'firstname'      => 'required|string|max:255',
            'middlename'     => 'required|string|max:255',
            'lastname'       => 'required|string|max:255',
            'course'         => 'required|string',
            'year_level'     => 'required|string',
            'status'         => 'required|string',
            'gender'         => 'required|string',
            'age'            => 'required|integer|min:1',
            'birthday'       => 'required|date',
            'contact_number' => 'required|regex:/^09\d{9}$/',
            'email'          => 'required|email|unique:tbl_StudentUser,email',
            'password'       => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',      // lowercase
                'regex:/[A-Z]/',      // uppercase
                'regex:/[0-9]/',      // digit
                'regex:/[\W_]/'       // special character
            ],
            'profile_picture'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Upload profile picture if provided
      //   $profilePath = null;
      //   if ($request->hasFile('profile_picture')) {
      //       $profilePath = $request->file('profile_picture')->store(path: 'profile_pictures', 'public');
      //   }

        // Create new student record
        $student = StudentUser::create([
            'edp_number'     => $request->edp_number,
            'firstname'      => $request->firstname,
            'middlename'     => $request->middlename,
            'lastname'       => $request->lastname,
            'course'         => $request->course,
            'year_level'     => $request->year_level,
            'status'         => $request->status,
            'gender'         => $request->gender,
            'age'            => $request->age,
            'birthday'       => $request->birthday,
            'contact_number' => $request->contact_number,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            // 'profile_picture'=> $profilePath,
        ]);

            // create QR content
         $qrContent = json_encode([
            'edp_number' => $student->edp_number,
            'firstname' => $student->firstname,
            'middlename' => $student->middlename,
            'lastname' => $student->lastname,
         ]);

         // generate the QR code image
         $fileName = 'qr_' . $student->id . '_' . $student->edp_number . '.svg';
         $savePath = public_path('uploads/Student_Qr_Code');

         file_put_contents($savePath . '/' . $fileName, QrCode::format('svg')->size(300)->generate($qrContent));

         // Save file path in DB
         $student->qr_code = 'uploads/Student_Qr_Code/' . $fileName;
         $student->save();


      if ($request->imageId > 0) {
         $tempImage = StudentProfilePicture::find($request->imageId);

         if ($tempImage != null) {
            $extArray = explode('.', $tempImage->name);
            $ext = last(array: $extArray);
            
            // create unique filename with model ID
            $fileName = strtotime("now") . $student->id . '.' . $ext;

            // Move from temp to final storage
            File::move(
               public_path("uploads/Student_Profile/" . $tempImage->name),
               public_path("uploads/Student_Profile_Image/" . $fileName)
            );

            // Save image path
            $student->profile_picture = $fileName;
            $student->save();

            // Clean temp DB
            $tempImage->delete();
         }
      }

      return response()->json([
         'status'  => true,
         'message' => 'Student registered successfully!',
         'data'    => $student
      ], 201);
   }

   public function index() {
      $studentUser = StudentUser::orderBy("created_at", "DESC")->get();

      return response()->json([
         "status" => true,
         "data" => $studentUser,
      ]);
   }
   
   public function approve($id)
   {
    $student = StudentUser::find($id);

    if (!$student) {
        return response()->json([
            'status' => false,
            'message' => 'Student not found.',
        ], 404);
    }

    $student->active_status = 1; 
    $student->save();

    Mail::to($student->email)->send(new StudentApprovedMail($student));

    return response()->json([
        'status' => true,
        'message' => 'Student has been approved.',
        'data' => $student
    ]);
   }

   public function disapprove($id)
   {
      $student = StudentUser::find($id);

      if (!$student) {
         return response()->json([
               'status' => false,
               'message' => 'Student not found.',
         ], 404);
      }

      $student->active_status = 0; 
      $student->save();

      return response()->json([
         'status' => true,
         'message' => 'Student has been disapproved.',
         'data' => $student
      ]);
   }

   public function destroy($id)
   {
      $student = StudentUser::find($id);

      if (!$student) {
         return response()->json([
               'status' => false,
               'message' => 'Student not found.',
         ], 404);
      }

      if ($student->profile_picture) {
         $imagePath = public_path("uploads/Student_Profile_Image/" . $student->profile_picture);
         if (File::exists($imagePath)) {
               File::delete($imagePath);
         }
      }

      if ($student->qr_code) {
         $qrPath = public_path($student->qr_code);
         if (File::exists($qrPath)) {
               File::delete($qrPath);
         }
      }

      $student->delete();

      return response()->json([
         'status' => true,
         'message' => 'Student has been deleted.'
      ]);
   }

   public function showQRCode($id)
   {
    $student = StudentUser::findOrFail($id);
    
    return view('emails.qrcode_card', [
        'student' => $student,
        'qr_code_url' => asset("{$student->qr_code}")
    ]);
}
}
