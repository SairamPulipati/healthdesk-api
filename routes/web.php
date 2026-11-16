<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleDashboardController;

// Public Root Redirects to Login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Role-Based Routes
Route::middleware(['auth'])->group(function () {
    
    // 1. Admin Routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', [RoleDashboardController::class, 'admin'])->name('admin.dashboard');
    });

    // 2. Doctor Routes
    Route::middleware(['role:doctor'])->group(function () {
        Route::get('/doctor/dashboard', [RoleDashboardController::class, 'doctor'])->name('doctor.dashboard');
        Route::post('/doctor/encounter/{id}/save', [RoleDashboardController::class, 'saveEncounter'])->name('doctor.encounter.save');
    });

    // 3. Nurse Routes
    Route::middleware(['role:nurse'])->group(function () {
        Route::get('/nurse/dashboard', [RoleDashboardController::class, 'nurse'])->name('nurse.dashboard');
        Route::post('/nurse/vitals/{id}/store', [RoleDashboardController::class, 'recordVitals'])->name('nurse.vitals.store');
    });

    // 4. Receptionist Routes
    Route::middleware(['role:receptionist'])->group(function () {
        Route::get('/reception/dashboard', [RoleDashboardController::class, 'receptionist'])->name('reception.dashboard');
        Route::post('/reception/checkin/{id}', [RoleDashboardController::class, 'checkInPatient'])->name('reception.checkin');
    });

    // 5. Patient Routes
    Route::middleware(['role:patient'])->group(function () {
        Route::get('/patient/dashboard', [RoleDashboardController::class, 'patient'])->name('patient.dashboard');
    });
});
