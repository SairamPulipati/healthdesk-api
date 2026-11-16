<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Encounter;
use App\Models\StaffProfile;
use Illuminate\Http\Request;

class RoleDashboardController extends Controller
{
    /**
     * 1. ADMIN DASHBOARD
     */
    public function admin()
    {
        $stats = [
            'total_users' => User::count(),
            'total_doctors' => User::where('role', 'doctor')->count(),
            'total_patients' => Patient::count(),
            'total_encounters' => Encounter::count(),
            'revenue_est' => Appointment::sum('copay_collected'),
        ];
        $recentUsers = User::with('staffProfile')->latest()->take(6)->get();
        return view('dashboards.admin', compact('stats', 'recentUsers'));
    }

    /**
     * 2. DOCTOR DASHBOARD (Provider Portal)
     */
    public function doctor()
    {
        $doctorId = auth()->id();
        $appointments = Appointment::with(['patient', 'encounter.prescriptions'])
            ->where('doctor_id', $doctorId)
            ->orderBy('appointment_time', 'asc')
            ->get();

        return view('dashboards.doctor', compact('appointments'));
    }

    /**
     * 3. NURSE DASHBOARD (Triage & Vitals Intake)
     */
    public function nurse()
    {
        $triageQueue = Appointment::with(['patient', 'doctor'])
            ->whereIn('status', ['checked_in', 'in_triage'])
            ->orderBy('appointment_time', 'asc')
            ->get();

        return view('dashboards.nurse', compact('triageQueue'));
    }

    /**
     * Record Vitals by Nurse
     */
    public function recordVitals(Request $request, $appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);

        $validated = $request->validate([
            'systolic_bp' => 'required|integer|min:50|max:260',
            'diastolic_bp' => 'required|integer|min:30|max:160',
            'pulse_rate' => 'required|integer|min:30|max:220',
            'temperature' => 'required|numeric|min:90|max:110',
            'oxygen_spo2' => 'required|integer|min:60|max:100',
            'weight_kg' => 'required|numeric|min:2|max:300',
        ]);

        $encounter = Encounter::firstOrCreate(
            ['appointment_id' => $appointment->id],
            [
                'patient_id' => $appointment->patient_id,
                'doctor_id' => $appointment->doctor_id,
            ]
        );

        $encounter->update(array_merge($validated, ['nurse_id' => auth()->id()]));
        $appointment->update(['status' => 'ready_for_doc']);

        return back()->with('success', "Vitals recorded for {$appointment->patient->full_name}. Patient marked: Ready for Doctor!");
    }

    /**
     * 4. RECEPTIONIST DASHBOARD (Front Desk Arrival Lobby)
     */
    public function receptionist()
    {
        $todayAppointments = Appointment::with(['patient', 'doctor.staffProfile'])
            ->orderBy('appointment_time', 'desc')
            ->get();

        $doctors = User::where('role', 'doctor')->with('staffProfile')->get();
        return view('dashboards.reception', compact('todayAppointments', 'doctors'));
    }

    /**
     * Front Desk Check-in Patient
     */
    public function checkInPatient(Request $request, $appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $copay = $request->input('copay_collected', 25.00);

        $appointment->update([
            'status' => 'checked_in',
            'copay_collected' => $copay,
        ]);

        return back()->with('success', "Patient {$appointment->patient->full_name} checked in! Copay of ₹{$copay} collected.");
    }

    /**
     * 5. PATIENT PORTAL
     */
    public function patient()
    {
        $patient = Patient::where('user_id', auth()->id())->first();
        $appointments = $patient
            ? Appointment::with(['doctor.staffProfile', 'encounter.prescriptions'])
                ->where('patient_id', $patient->id)
                ->latest()
                ->get()
            : collect();

        return view('dashboards.patient', compact('patient', 'appointments'));
    }

    /**
     * Doctor Saves SOAP Notes & Signs Encounter
     */
    public function saveEncounter(Request $request, $appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);

        $validated = $request->validate([
            'subjective_notes' => 'required|string',
            'objective_notes' => 'required|string',
            'assessment' => 'required|string',
            'clinical_plan' => 'required|string',
        ]);

        $encounter = Encounter::firstOrCreate(
            ['appointment_id' => $appointment->id],
            [
                'patient_id' => $appointment->patient_id,
                'doctor_id' => $appointment->doctor_id,
            ]
        );

        $encounter->update(array_merge($validated, [
            'is_signed' => true,
            'signed_at' => now(),
            'signed_by_doctor' => auth()->user()->name,
        ]));

        $appointment->update(['status' => 'completed']);

        return back()->with('success', "Clinical Encounter signed and locked. Patient visit marked as Completed!");
    }
}
