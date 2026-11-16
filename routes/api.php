<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Encounter;
use Illuminate\Support\Facades\Hash;

/**
 * 1. Health & Status Ping
 */
Route::get('/health', function () {
    return response()->json([
        'status' => 'UP',
        'system' => 'HealthDesk Clinical Core API',
        'timestamp' => now()->toISOString(),
    ]);
});

/**
 * 2. Mobile/External Client Token Authentication
 */
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid clinical credentials provided.',
        ], 401);
    }

    $token = $user->createToken('clinical-api-token')->plainTextToken;

    return response()->json([
        'success' => true,
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]
    ]);
});

/**
 * 3. Authenticated Clinical REST API Routes
 */
Route::middleware('auth:sanctum')->group(function () {
    
    // Get Current Authenticated Staff/Patient Profile
    Route::get('/user', function (Request $request) {
        return $request->user()->load(['staffProfile', 'patientProfile']);
    });

    // Patient Directory API
    Route::get('/patients', function () {
        return response()->json([
            'success' => true,
            'data' => Patient::latest()->get()
        ]);
    });

    // Appointments Queue API
    Route::get('/appointments', function () {
        return response()->json([
            'success' => true,
            'data' => Appointment::with(['patient', 'doctor'])->latest()->get()
        ]);
    });
});
