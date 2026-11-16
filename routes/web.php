<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleDashboardController;
use App\Http\Controllers\PatientManagementController;
use App\Http\Controllers\StaffManagementController;
use App\Http\Controllers\UserProfileController;

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
    
    // Shared: Profile Self-Service (Edit address, DOB, change password)
    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [UserProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [UserProfileController::class, 'updatePassword'])->name('profile.password');

    // Shared: Booking & Cancelling Appointments
    Route::post('/appointments/book', [RoleDashboardController::class, 'bookAppointment'])->name('appointments.book');
    Route::post('/appointments/{id}/cancel', [RoleDashboardController::class, 'cancelAppointment'])->name('appointments.cancel');

    // 1. Admin Routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', [RoleDashboardController::class, 'admin'])->name('admin.dashboard');
        Route::get('/admin/staff', [StaffManagementController::class, 'index'])->name('admin.staff.index');
        Route::post('/admin/staff', [StaffManagementController::class, 'store'])->name('admin.staff.store');
        Route::put('/admin/staff/{id}', [StaffManagementController::class, 'update'])->name('admin.staff.update');
        Route::post('/admin/staff/{id}/toggle', [StaffManagementController::class, 'toggleStatus'])->name('admin.staff.toggle');
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

    // 5. Patient Master Directory (Add, View, Edit, Mark Deceased/Delete Patients)
    Route::middleware(['role:admin,receptionist,doctor,nurse'])->group(function () {
        Route::get('/patients', [PatientManagementController::class, 'index'])->name('patients.index');
        Route::post('/patients', [PatientManagementController::class, 'store'])->name('patients.store');
        Route::put('/patients/{id}', [PatientManagementController::class, 'update'])->name('patients.update');
        Route::delete('/patients/{id}', [PatientManagementController::class, 'destroy'])->name('patients.destroy');
    });

    // 6. Patient Portal
    Route::middleware(['role:patient'])->group(function () {
        Route::get('/patient/dashboard', [RoleDashboardController::class, 'patient'])->name('patient.dashboard');
    });
});
