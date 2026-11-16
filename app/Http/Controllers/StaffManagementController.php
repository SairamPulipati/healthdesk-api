<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\StaffProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffManagementController extends Controller
{
    /**
     * List all staff accounts for Admin
     */
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['doctor', 'nurse', 'receptionist', 'admin'])->with('staffProfile');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $staff = $query->latest()->paginate(15);
        return view('admin.staff-index', compact('staff'));
    }

    /**
     * Add new staff member (Hire)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:doctor,nurse,receptionist,admin',
            'department' => 'required|string|max:100',
            'specialization' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'consultation_fee' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        $prefix = match($validated['role']) {
            'doctor' => 'DOC-',
            'nurse' => 'NUR-',
            'receptionist' => 'REC-',
            default => 'ADM-',
        };
        $code = $prefix . str_pad($user->id, 4, '0', STR_PAD_LEFT);

        StaffProfile::create([
            'user_id' => $user->id,
            'employee_code' => $code,
            'department' => $validated['department'],
            'specialization' => $validated['specialization'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'consultation_fee' => $validated['consultation_fee'] ?? 0.00,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'is_on_duty' => true,
            'employment_status' => 'active',
        ]);

        return back()->with('success', "Staff account for {$user->name} ({$code}) created successfully!");
    }

    /**
     * Update Staff Profile & Password
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:100',
            'specialization' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'consultation_fee' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'new_password' => 'nullable|min:6',
        ]);

        $user->update(['name' => $validated['name']]);

        if (!empty($validated['new_password'])) {
            $user->update(['password' => Hash::make($validated['new_password'])]);
        }

        if ($user->staffProfile) {
            $user->staffProfile->update([
                'department' => $validated['department'],
                'specialization' => $validated['specialization'] ?? $user->staffProfile->specialization,
                'phone' => $validated['phone'] ?? $user->staffProfile->phone,
                'consultation_fee' => $validated['consultation_fee'] ?? $user->staffProfile->consultation_fee,
                'address' => $validated['address'] ?? $user->staffProfile->address,
                'city' => $validated['city'] ?? $user->staffProfile->city,
                'date_of_birth' => $validated['date_of_birth'] ?? $user->staffProfile->date_of_birth,
            ]);
        }

        return back()->with('success', "Staff record for {$user->name} updated successfully.");
    }

    /**
     * Toggle Staff Enable / Disable (or Left Firm)
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent disabling self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot disable your own administrator account.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        if ($user->staffProfile) {
            $user->staffProfile->employment_status = $user->is_active ? 'active' : 'resigned';
            $user->staffProfile->save();
        }

        $statusStr = $user->is_active ? 'Enabled / Active' : 'Disabled / Left Firm';
        return back()->with('success', "Account for {$user->name} is now {$statusStr}.");
    }
}
