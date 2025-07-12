<?php

namespace App\Http\Controllers;

use App\Models\HeroSlider;
use App\Models\HeroSliderImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class HeroSliderController extends Controller
{
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "title" => "required|string|max:255|unique:tbl_HeroSlider,title",
            "description" => "required|string|max:255|unique:tbl_HeroSlider,description",
            "content" => "required|string",
            "image" => "nullable|image|mimes:jpg,jpeg,png,gif",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "errors" => $validator->errors(),
            ]);
        }

        $model = new HeroSlider();
        $model->title = $request->title;
        $model->description = $request->description;
        $model->content = $request->content;
        $model->save();

        if ($request->imageId > 0) {
            $tempImage = HeroSliderImage::find($request->imageId);

            if ($tempImage != null) {
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                // create unique filename with model ID
                $fileName = strtotime("now") . $model->id . '.' . $ext;

                // Move from temp to final storage
                File::move(
                    public_path("uploads/Hero_Slider/" . $tempImage->name),
                    public_path("uploads/Hero_Slider_Image/" . $fileName)
                );

                // Save image path
                $model->image = $fileName;
                $model->save();

                // Clean temp DB
                $tempImage->delete();
            }
        }


        return response()->json([
            "status" => true,
            "message" => "Hero Slider created successfully.",
            "data" => $model,
        ]);

    }

    public function index(Request $request)
    {
        $query = HeroSlider::query()->orderBy("created_at", "DESC");

        // Search filter (if you want to implement search functionality)
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                  ->orWhere('description', 'like', '%'.$request->search.'%');
            });
        }

        // Get pagination parameters
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);

        $sliders = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $sliders->items(),
            'meta' => [
                'total' => $sliders->total(),
                'per_page' => $sliders->perPage(),
                'current_page' => $sliders->currentPage(),
                'last_page' => $sliders->lastPage(),
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $heroSlider = HeroSlider::find($id);

        if ($heroSlider == null) {
            return response()->json([
                "status" => false,
                "message" => "Hero slider not found",
                ]);
        }

        $validator = Validator::make($request->all(), [
            "title" => "required|string|max:255|unique:tbl_HeroSlider,title," . $id,
            "description" => "required|string|max:255|unique:tbl_HeroSlider,description," . $id,
            "content" => "required|string",
            "image" => "nullable|image|mimes:jpg,jpeg,png,gif",
        ]);


        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "errors" => $validator->errors(),
            ]);
        }

        $heroSlider->title = $request->title;
        $heroSlider->description = $request->description;
        $heroSlider->content = $request->content;
        $heroSlider->save();

        if ($request->imageId > 0) {
            $tempImage = HeroSliderImage::find($request->imageId);

            if ($tempImage != null) {
                // Delete old image (if exists)
                if (!empty($heroSlider->image)) {
                    $oldPath = public_path("uploads/Hero_Slider_Image/" . $heroSlider->image);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }

                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);
                $fileName = strtotime("now") . $heroSlider->id . '.' . $ext;

                File::move(
                    public_path("uploads/Hero_Slider/" . $tempImage->name),
                    public_path("uploads/Hero_Slider_Image/" . $fileName)
                );

                $heroSlider->image = $fileName;
                $heroSlider->save();

                $tempImage->delete();
            }
        }

        return response()->json([
            "status" => true,
            "message" => "Hero slider updated successfully",
        ]);
    }

    public function show($id)
    {
        $heroSlider = HeroSlider::find($id);

        if ($heroSlider == null) {
            return response()->json([
                "status" => false,
                "message" => "Hero Slider not found",
            ]);
        }

        return response()->json([
            "status" => true,
            "data" => $heroSlider,
        ]);
    }

    public function destroy($id)
    {
        $heroSlider = HeroSlider::find($id);

        if ($heroSlider == null) {
            return response()->json([
                "status" => false,
                "message" => "Hero slider not found.",
            ]);
        }

        $heroSlider->delete();

        return response()->json([
            "status" => true,
            "message" => "Hero Slider deleted successfully",
        ]);
    }
}
