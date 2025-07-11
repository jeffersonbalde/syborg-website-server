<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Event\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\client\ClientHeroSliderController;
use App\Http\Controllers\HeroSliderController;
use App\Http\Controllers\HeroSliderImageController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Student\StudentProfilePictureController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Route::post("authenticate", [AuthenticationController::class, "authenticate"]);

Route::get("get-hero-slider", [ClientHeroSliderController::class, "index"]);

Route::post("register", [StudentController::class, "store"]);
Route::post("student-profile-picture", [StudentProfilePictureController::class, "store"]);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        $role = match(get_class($user)) {
            \App\Models\AdminUser::class => 'admin',
            \App\Models\StudentUser::class => 'student',
            \App\Models\OwnerUser::class => 'owner',
            default => 'unknown'
        };

        return response()->json([
            'user' => $user,
            'role' => $role
        ]);
    });
    Route::post('/logout', [AuthenticationController::class, 'logout']);

    // HeroSlider routes
    Route::post("heroslider", [HeroSliderController::class, "store"]);
    Route::get("heroslider", [HeroSliderController::class, "index"]);
    Route::put("heroslider/{id}", [HeroSliderController::class, "update"]);
    Route::get("heroslider/{id}", [HeroSliderController::class, "show"]);
    Route::delete("heroslider/{id}", [HeroSliderController::class, "destroy"]);

    // HeroSlider Image routes
    Route::post("hero-slider-image", [HeroSliderImageController::class, "store"]);

    // Student routes
    Route::get("students", [StudentController::class, "index"]);
    Route::put('/students/{id}/approve', [StudentController::class, 'approve']);
    Route::put('/students/{id}/disapprove', [StudentController::class, 'disapprove']);
    Route::delete('/students/{id}', [StudentController::class, 'destroy']);

    // Dashboard routes
    Route::get('/admin/dashboard-stats', [StudentController::class,'getStats']);

    // Event routes
    Route::post("events", [EventController::class, "store"]);
    Route::get("events", [EventController::class, "index"]);
});

Route::group(["middleware" => ["auth:sanctum"]], function () {
    // Protected routes
    Route::get("dashboard", [DashboardController::class, "index"]);
    Route::get("logout", [AuthenticationController::class, "logout"]);
});

Route::get('/users', function () {
    return User::all();
});
