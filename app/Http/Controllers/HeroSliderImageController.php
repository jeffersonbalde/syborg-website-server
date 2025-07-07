<?php

namespace App\Http\Controllers;

use App\Models\HeroSliderImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HeroSliderImageController extends Controller
{
    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
            "image" => "required|mimes:png,jpg,jpeg,gif"
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => false,
                "errors" => $validator->errors("image"),
            ]);
        }

        $image = $request->image;

        if(!empty($image)) {
            $extension = $image->getClientOriginalExtension();
            

            $imageName = strtotime("now").".".$extension;

            $model = new HeroSliderImage();
            $model->name = $imageName;
            $model->save();

            $image->move(public_path("uploads/Hero_Slider"), $imageName);
            
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
