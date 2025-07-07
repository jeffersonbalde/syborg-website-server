<?php

use App\Http\Controllers\AuthenticationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::post('/login', [AuthenticationController::class, 'login']);
// Route::post('/logout', function (Request $request) {
//     Auth::guard('web')->logout();
//     $request->session()->invalidate();
//     $request->session()->regenerateToken();
//     return response()->json(['status' => true, 'message' => 'Logged out']);
// });

Route::middleware(['web'])->group(function () {
    Route::post('/login', [AuthenticationController::class, 'login']) ->middleware('throttle:5,1');;
});
