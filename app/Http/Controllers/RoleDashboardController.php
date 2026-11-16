<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Encounter;
use App\Models\Prescription;
use App\Mail\AppointmentNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

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
        $recentUsers = User::with('staffProfile')->latest()->take(8)->get();
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
     * Doctor Saves SOAP Notes, Medical Conditions, Prescriptions, and Signs Encounter
     */
    public function saveEncounter(Request $request, $appointmentId)
    {
        $appointment = Appointment::with('patient')->findOrFail($appointmentId);

        // Security Guard: Prevent editing if already locked
        if ($appointment->encounter && $appointment->encounter->is_signed) {
            return back()->with('error', 'This clinical encounter is signed and permanently locked.');
        }

        $validated = $request->validate([
            'subjective_notes' => 'required|string',
            'objective_notes' => 'required|string',
            'assessment' => 'required|string',
            'clinical_plan' => 'required|string',
            'allergies' => 'nullable|string',
            'medical_problems' => 'nullable|string',
            'medication_name' => 'nullable|string',
            'dosage' => 'nullable|string',
            'frequency' => 'nullable|string',
            'duration_days' => 'nullable|integer',
        ]);

        // Update Patient's permanent allergies & medical problems
        if (!empty($validated['allergies']) || !empty($validated['medical_problems'])) {
            $appointment->patient->update([
                'allergies' => $validated['allergies'] ?? $appointment->patient->allergies,
                'medical_problems' => $validated['medical_problems'] ?? $appointment->patient->medical_problems,
            ]);
        }

        $encounter = Encounter::firstOrCreate(
            ['appointment_id' => $appointment->id],
            [
                'patient_id' => $appointment->patient_id,
                'doctor_id' => $appointment->doctor_id,
            ]
        );

        $encounter->update([
            'subjective_notes' => $validated['subjective_notes'],
            'objective_notes' => $validated['objective_notes'],
            'assessment' => $validated['assessment'],
            'clinical_plan' => $validated['clinical_plan'],
            'is_signed' => true,
            'signed_at' => now(),
            'signed_by_doctor' => auth()->user()->name,
        ]);

        // Add Prescription if entered
        if (!empty($validated['medication_name'])) {
            Prescription::create([
                'encounter_id' => $encounter->id,
                'medication_name' => $validated['medication_name'],
                'dosage' => $validated['dosage'] ?? '500mg',
                'frequency' => $validated['frequency'] ?? 'Twice daily',
                'duration_days' => $validated['duration_days'] ?? 5,
            ]);
        }

        $appointment->update(['status' => 'completed']);

        // Dispatch Free Automated Email Notification
        if ($appointment->patient->email) {
            try {
                Mail::to($appointment->patient->email)->send(
                    new AppointmentNotificationMail($appointment, 'completed')
                );
            } catch (\Exception $e) {
                // Silently logged
            }
        }

        return back()->with('success', "Encounter #{$encounter->id} signed and permanently locked. Clinical visit completed!");
    }

    /**
     * 3. NURSE DASHBOARD (Triage Station)
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
     * 4. RECEPTIONIST DASHBOARD
     */
    public function receptionist()
    {
        $todayAppointments = Appointment::with(['patient', 'doctor.staffProfile'])
            ->orderBy('appointment_time', 'desc')
            ->get();

        $doctors = User::where('role', 'doctor')->where('is_active', true)->with('staffProfile')->get();
        $patients = Patient::where('patient_status', 'active')->orderBy('first_name')->get();

        return view('dashboards.reception', compact('todayAppointments', 'doctors', 'patients'));
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

        // Dispatch Email
        if ($appointment->patient->email) {
            try {
                Mail::to($appointment->patient->email)->send(
                    new AppointmentNotificationMail($appointment, 'confirmed')
                );
            } catch (\Exception $e) {}
        }

        return back()->with('success', "Patient {$appointment->patient->full_name} checked in! Copay of ₹{$copay} collected.");
    }

    /**
     * Book Appointment with Conflict-Check Guard
     */
    public function bookAppointment(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'appointment_time' => 'required|date|after:now',
            'chief_complaint' => 'nullable|string|max:1000',
            'copay_collected' => 'nullable|numeric|min:0',
        ]);

        // CONFLICT GUARD: Check if Doctor is already booked within 30 minutes of this slot
        $slotTime = Carbon::parse($validated['appointment_time']);
        $conflictStart = $slotTime->copy()->subMinutes(29);
        $conflictEnd = $slotTime->copy()->addMinutes(29);

        $conflictExists = Appointment::where('doctor_id', $validated['doctor_id'])
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->whereBetween('appointment_time', [$conflictStart, $conflictEnd])
            ->exists();

        if ($conflictExists) {
            $doctor = User::find($validated['doctor_id']);
            return back()->with('error', "Scheduling Conflict: {$doctor->name} already has a consultation scheduled around {$slotTime->format('h:i A')}. Please choose another time slot.");
        }

        $appointment = Appointment::create($validated);

        // Send Free Automated Email Notification
        if ($appointment->patient->email) {
            try {
                Mail::to($appointment->patient->email)->send(
                    new AppointmentNotificationMail($appointment, 'booked')
                );
            } catch (\Exception $e) {}
        }

        return back()->with('success', "Appointment #{$appointment->id} scheduled successfully for {$appointment->patient->full_name}!");
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

        $doctors = User::where('role', 'doctor')->where('is_active', true)->with('staffProfile')->get();

        return view('dashboards.patient', compact('patient', 'appointments', 'doctors'));
    }

    /**
     * Cancel an Appointment (Only if not yet completed)
     */
    public function cancelAppointment($id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->status === 'completed') {
            return back()->with('error', 'Completed clinical encounters cannot be cancelled.');
        }

        $appointment->update(['status' => 'cancelled']);

        if ($appointment->patient->email) {
            try {
                Mail::to($appointment->patient->email)->send(
                    new AppointmentNotificationMail($appointment, 'cancelled')
                );
            } catch (\Exception $e) {}
        }

        return back()->with('success', "Appointment #{$appointment->id} marked as Cancelled.");
    }
}
