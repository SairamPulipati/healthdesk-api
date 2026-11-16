<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StaffProfile;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Encounter;
use App\Models\Prescription;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Hospital Administrator
        $admin = User::create([
            'name' => 'Hospital Admin',
            'email' => 'admin@healthdesk.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 2. Create Attending Physician (Doctor)
        $doctor = User::create([
            'name' => 'Dr. Sairam Pulipati',
            'email' => 'doctor@healthdesk.com',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
        ]);
        StaffProfile::create([
            'user_id' => $doctor->id,
            'employee_code' => 'DOC-001',
            'department' => 'Cardiology',
            'specialization' => 'Interventional Cardiologist',
            'consultation_fee' => 750.00,
            'phone' => '+91 9876543210',
            'is_on_duty' => true,
        ]);

        // 3. Create Triage Nurse
        $nurse = User::create([
            'name' => 'Nurse Ananya Sharma',
            'email' => 'nurse@healthdesk.com',
            'password' => Hash::make('password123'),
            'role' => 'nurse',
        ]);
        StaffProfile::create([
            'user_id' => $nurse->id,
            'employee_code' => 'NUR-010',
            'department' => 'Outpatient Triage',
            'phone' => '+91 9123456780',
            'is_on_duty' => true,
        ]);

        // 4. Create Front Desk Receptionist
        $receptionist = User::create([
            'name' => 'Pooja Reddy (Front Desk)',
            'email' => 'reception@healthdesk.com',
            'password' => Hash::make('password123'),
            'role' => 'receptionist',
        ]);
        StaffProfile::create([
            'user_id' => $receptionist->id,
            'employee_code' => 'REC-101',
            'department' => 'Patient Registration & Billing',
            'phone' => '+91 9000112233',
            'is_on_duty' => true,
        ]);

        // 5. Create Patient Portal User (Ravi Kumar)
        $patientUser = User::create([
            'name' => 'Ravi Kumar',
            'email' => 'patient@healthdesk.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
        ]);
        $patient = Patient::create([
            'user_id' => $patientUser->id,
            'mrn' => 'MRN-2026-001',
            'first_name' => 'Ravi',
            'last_name' => 'Kumar',
            'date_of_birth' => '1988-06-14',
            'gender' => 'male',
            'phone' => '+91 9876543211',
            'email' => 'patient@healthdesk.com',
            'blood_group' => 'B+',
            'pcp_name' => 'Dr. Ramesh Naidu (Internal Medicine)',
            'allergies' => 'Severe Penicillin Allergy (Anaphylaxis risk), Shellfish',
            'insurance_payer' => 'Aetna Medicare Advantage',
            'insurance_policy_no' => 'AET-9928192-01',
        ]);

        // 6. Create Live Clinical Appointment (Scheduled for Today)
        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_time' => now()->addHours(2),
            'status' => 'in_consultation',
            'chief_complaint' => 'Persistent retrosternal chest pain on exertion, mild shortness of breath.',
            'copay_collected' => 25.00,
        ]);

        // 7. Clinical Encounter with Vitals and SOAP Notes
        $encounter = Encounter::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'nurse_id' => $nurse->id,
            'systolic_bp' => 138,
            'diastolic_bp' => 88,
            'pulse_rate' => 82,
            'temperature' => 98.6,
            'oxygen_spo2' => 98,
            'weight_kg' => 74.5,
            'subjective_notes' => 'Patient reports 4-day history of chest tightness after climbing stairs. No radiating pain to left jaw.',
            'objective_notes' => 'S1 and S2 present. No murmurs. Bilateral breath sounds clear. ECG shows normal sinus rhythm with mild ST depression in V4-V6.',
            'assessment' => 'Primary: I20.9 (Angina pectoris, unspecified). Secondary: I10 (Essential hypertension).',
            'clinical_plan' => 'Prescribed Amlodipine & Aspirin. Advised 2D Echocardiogram. Follow-up in 14 days.',
            'is_signed' => true,
            'signed_at' => now(),
            'signed_by_doctor' => 'Dr. Sairam Pulipati, MD (Cardiology)',
        ]);

        // 8. Prescriptions
        Prescription::create([
            'encounter_id' => $encounter->id,
            'medication_name' => 'Amlodipine Besylate',
            'dosage' => '5mg',
            'frequency' => 'Once daily in the morning',
            'duration_days' => 30,
            'instructions' => 'Take with water before breakfast.',
        ]);
        Prescription::create([
            'encounter_id' => $encounter->id,
            'medication_name' => 'Ecosprin (Aspirin)',
            'dosage' => '75mg',
            'frequency' => 'Once daily after dinner',
            'duration_days' => 30,
            'instructions' => 'Take strictly after meals to prevent gastric irritation.',
        ]);
    }
}
