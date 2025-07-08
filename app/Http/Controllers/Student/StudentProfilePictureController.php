<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentProfilePicture;
use App\Models\StudentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentProfilePictureController extends Controller
{
    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
            "image" => "required|mimes:png,jpg,jpeg,gif"
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => false,
                "errors" => $validator->errors(),
            ]);
        }

        $image = $request->image;

        if(!empty($image)) {
            $extension = $image->getClientOriginalExtension();


            $imageName = strtotime("now").".".$extension;

            $model = new StudentProfilePicture();
            $model->name = $imageName;
            $model->save();

            $image->move(public_path("uploads/Student_Profile"), $imageName);
            
            // $sourcePath = public_path("uploads/temp/".$imageName);
            // $destinationPath = public_path("uploads/temp/thumb/".$imageName);
            // $manager = new ImageManager(Driver::class);
            // $image = $manager->read($sourcePath);
            // $image->coverDown(300, 300);
            // $image->save($destinationPath);

            return response()->json([
                "status" => true,
                "message" => "Image uploaded successfully.",
                "data" => $model,
            ]);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Image is required.",
            ]);
        }
    }
    
}
