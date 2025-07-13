<?php

use App\Http\Controllers\Admin\Attendance\AttendanceController;
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

// Public routes
Route::get("get-hero-slider", [ClientHeroSliderController::class, "index"]);
Route::post("register", [StudentController::class, "store"]);
Route::post("student-profile-picture", [StudentProfilePictureController::class, "store"]);

// Authenticated routes (sanctum protected)
Route::middleware(['auth:sanctum'])->group(function () {
    // User routes
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
    Route::apiResource("heroslider", HeroSliderController::class)->except(['update']);
    Route::put("heroslider/{id}", [HeroSliderController::class, "update"]);
    Route::post("hero-slider-image", [HeroSliderImageController::class, "store"]);

    // Student routes
    Route::get("students", [StudentController::class, "index"]);
    Route::put('/students/{id}/approve', [StudentController::class, 'approve']);
    Route::put('/students/{id}/disapprove', [StudentController::class, 'disapprove']);
    Route::delete('/students/{id}', [StudentController::class, 'destroy']);
    Route::get('/student/attendance-records', [StudentController::class, 'getAttendanceRecords']);

    // Dashboard routes
    Route::get('/admin/dashboard-stats', [StudentController::class,'getStats']);
    Route::get("dashboard", [DashboardController::class, "index"]);

    // Event routes
    Route::apiResource("events", EventController::class);

    // Attendance routes
    Route::prefix('attendance')->group(function () {
        Route::get('events', [EventController::class, 'getEventsForAttendance']);
        Route::get('{event}', [AttendanceController::class, 'getEventDetails']);
        Route::post('manual', [AttendanceController::class, 'manualAttendance']);
        Route::post('bulk', [AttendanceController::class, 'saveBulkAttendance']);
        Route::post('scan', [AttendanceController::class, 'scanAttendance']);
        Route::delete('{attendance}', [AttendanceController::class, 'removeAttendance']);
    });
});

// Debug route (should probably be removed in production)
Route::get('/users', function () {
    return User::all();
});
