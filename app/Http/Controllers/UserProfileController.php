<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserProfileController extends Controller
{
    /**
     * Show self profile edit form
     */
    public function edit()
    {
        $user = auth()->user()->load(['staffProfile', 'patientProfile']);
        return view('profile.edit', compact('user'));
    }

    /**
     * Update non-breaking profile fields (Address, DOB, Emergency Contact)
     * Email and Phone are strictly read-only for anti-fraud protection
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:150',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'blood_group' => 'nullable|string|max:5',
        ]);

        // If staff (Doctor, Nurse, Receptionist)
        if ($user->staffProfile) {
            $user->staffProfile->update([
                'date_of_birth' => $validated['date_of_birth'] ?? $user->staffProfile->date_of_birth,
                'address' => $validated['address'] ?? $user->staffProfile->address,
                'city' => $validated['city'] ?? $user->staffProfile->city,
            ]);
        }

        // If patient
        if ($user->patientProfile) {
            $user->patientProfile->update([
                'date_of_birth' => $validated['date_of_birth'] ?? $user->patientProfile->date_of_birth,
                'address' => $validated['address'] ?? $user->patientProfile->address,
                'city' => $validated['city'] ?? $user->patientProfile->city,
                'state' => $validated['state'] ?? $user->patientProfile->state,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? $user->patientProfile->emergency_contact_name,
                'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? $user->patientProfile->emergency_contact_phone,
                'blood_group' => $validated['blood_group'] ?? $user->patientProfile->blood_group,
            ]);
        }

        return back()->with('success', 'Your demographic profile details have been saved.');
    }

    /**
     * Change Password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Your current password does not match our records.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Your account password has been updated securely.');
    }
}
