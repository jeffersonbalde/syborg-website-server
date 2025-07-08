<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use App\Models\StudentUser;
use App\Models\OwnerUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    public function authenticate(Request $request) {

        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "errors" => $validator->errors()
            ], 422);
        }

        $guards = [
            'admin' => AdminUser::class,
            'student' => StudentUser::class,
            'owner' => OwnerUser::class,
        ];

        foreach ($guards as $role => $model) {
            $user = $model::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                // Create a token
                $token = $user->createToken($role . '-token')->plainTextToken;

                return response()->json([
                    "status" => true,
                    "message" => "Login successfully.",
                    "role" => $role,
                    "token" => $token,
                    "user" => $user
                ]);
            }
        }

        return response()->json([
            "status" => false,
            "message" => "Invalid credentials."
        ], 401);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        foreach (['admin', 'student'] as $guard) {
            if (Auth::guard($guard)->attempt($request->only('email', 'password'))) {
                $user = Auth::guard($guard)->user();

                if ($guard === 'student') {
                    if ($user->active_status == 2) {
                        Auth::guard($guard)->logout();
                        return response()->json([
                            'status' => false,
                            'message' => 'Your registration is still pending approval. Please wait for the administrator to review your request.',
                        ], 403);
                    }

                    if ($user->active_status == 0) {
                        Auth::guard($guard)->logout();
                        return response()->json([
                            'status' => false,
                            'message' => 'Your registration has been disapproved by the administrator. Please contact the Club President for further details.',
                        ], 403);
                    }
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Login successfully.',
                    'role' => $guard,
                    'user' => $user,
                    'token' => $user->createToken('auth-token')->plainTextToken
                ]);
            }
        }

        return response()->json(['status' => false, 'message' => 'Invalid credentials.'], 401);
    }


    public function logout(Request $request) {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'No authenticated user found.',
            ], 401);
        }

        $token = $user->currentAccessToken();

        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        if ($token) {
            $token->delete(); // Revoke token
        }

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully.',
        ]);
    }
}
