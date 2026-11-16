<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientManagementController extends Controller
{
    /**
     * List all patients with search filter
     */
    public function index(Request $request)
    {
        $query = Patient::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('mrn', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
        }

        $patients = $query->latest()->paginate(15);
        return view('patients.index', compact('patients'));
    }

    /**
     * Store new patient registration (Auto MRN generation)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'nullable|date',
            'gender' => 'required|in:male,female,other',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:150',
            'blood_group' => 'nullable|string|max:5',
            'pcp_name' => 'nullable|string|max:150',
            'allergies' => 'nullable|string|max:1000',
            'insurance_payer' => 'required|string|max:100',
            'insurance_policy_no' => 'nullable|string|max:100',
        ]);

        // Auto-generate Unique Medical Record Number (e.g. PAT-2026-0042)
        $latestId = Patient::max('id') + 1;
        $mrn = 'MRN-' . date('Y') . '-' . str_pad($latestId, 4, '0', STR_PAD_LEFT);

        $patient = Patient::create(array_merge($validated, ['mrn' => $mrn]));

        return back()->with('success', "Patient {$patient->full_name} registered successfully with MRN: {$patient->mrn}!");
    }

    /**
     * Update existing patient demographics
     */
    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'nullable|date',
            'gender' => 'required|in:male,female,other',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:150',
            'blood_group' => 'nullable|string|max:5',
            'pcp_name' => 'nullable|string|max:150',
            'allergies' => 'nullable|string|max:1000',
            'insurance_payer' => 'required|string|max:100',
            'insurance_policy_no' => 'nullable|string|max:100',
        ]);

        $patient->update($validated);

        return back()->with('success', "Patient records for {$patient->full_name} ({$patient->mrn}) updated successfully!");
    }

    /**
     * Delete patient record (Admin & Front Desk supervisor)
     */
    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $name = $patient->full_name;
        $patient->delete();

        return back()->with('success', "Patient record for {$name} deleted successfully.");
    }
}
