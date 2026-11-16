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
        // 1. Hospital Administrator
        $admin = User::create([
            'name' => 'Hospital Admin',
            'email' => 'admin@healthdesk.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // 2. DOCTOR 1: Dr. Sairam Pulipati (Cardiology)
        $doc1 = User::create([
            'name' => 'Dr. Sairam Pulipati',
            'email' => 'doctor@healthdesk.com',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);
        StaffProfile::create([
            'user_id' => $doc1->id,
            'employee_code' => 'DOC-001',
            'department' => 'Cardiology',
            'specialization' => 'Interventional Cardiologist',
            'consultation_fee' => 750.00,
            'phone' => '+91 9876543210',
            'date_of_birth' => '1985-05-12',
            'city' => 'Hyderabad',
            'is_on_duty' => true,
            'employment_status' => 'active',
        ]);

        // 3. DOCTOR 2: Dr. Priya Sharma (Internal Medicine / General Physician)
        $doc2 = User::create([
            'name' => 'Dr. Priya Sharma',
            'email' => 'priya.sharma@healthdesk.com',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);
        StaffProfile::create([
            'user_id' => $doc2->id,
            'employee_code' => 'DOC-002',
            'department' => 'General Medicine',
            'specialization' => 'Senior Consultant Physician',
            'consultation_fee' => 500.00,
            'phone' => '+91 9876543222',
            'date_of_birth' => '1989-08-20',
            'city' => 'Hyderabad',
            'is_on_duty' => true,
            'employment_status' => 'active',
        ]);

        // 4. DOCTOR 3: Dr. Rajesh Varma (Pediatrics & Child Care)
        $doc3 = User::create([
            'name' => 'Dr. Rajesh Varma',
            'email' => 'rajesh.varma@healthdesk.com',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);
        StaffProfile::create([
            'user_id' => $doc3->id,
            'employee_code' => 'DOC-003',
            'department' => 'Pediatrics',
            'specialization' => 'Pediatric Cardiologist',
            'consultation_fee' => 600.00,
            'phone' => '+91 9876543333',
            'date_of_birth' => '1982-11-04',
            'city' => 'Hyderabad',
            'is_on_duty' => true,
            'employment_status' => 'active',
        ]);

        // 5. NURSE 1: Nurse Ananya Sharma (Triage Lead)
        $nurse1 = User::create([
            'name' => 'Nurse Ananya Sharma',
            'email' => 'nurse@healthdesk.com',
            'password' => Hash::make('password123'),
            'role' => 'nurse',
            'is_active' => true,
        ]);
        StaffProfile::create([
            'user_id' => $nurse1->id,
            'employee_code' => 'NUR-010',
            'department' => 'Outpatient Triage',
            'phone' => '+91 9123456780',
            'city' => 'Hyderabad',
            'is_on_duty' => true,
            'employment_status' => 'active',
        ]);

        // 6. NURSE 2: Nurse Vikram Rathore
        $nurse2 = User::create([
            'name' => 'Nurse Vikram Rathore',
            'email' => 'vikram.nurse@healthdesk.com',
            'password' => Hash::make('password123'),
            'role' => 'nurse',
            'is_active' => true,
        ]);
        StaffProfile::create([
            'user_id' => $nurse2->id,
            'employee_code' => 'NUR-011',
            'department' => 'Emergency Triage',
            'phone' => '+91 9123456788',
            'city' => 'Hyderabad',
            'is_on_duty' => true,
            'employment_status' => 'active',
        ]);

        // 7. RECEPTIONIST: Pooja Reddy (Front Desk)
        $receptionist = User::create([
            'name' => 'Pooja Reddy (Front Desk)',
            'email' => 'reception@healthdesk.com',
            'password' => Hash::make('password123'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);
        StaffProfile::create([
            'user_id' => $receptionist->id,
            'employee_code' => 'REC-101',
            'department' => 'Patient Registration & Billing',
            'phone' => '+91 9000112233',
            'city' => 'Hyderabad',
            'is_on_duty' => true,
            'employment_status' => 'active',
        ]);

        // 8. PATIENT 1: Ravi Kumar (Active Cardiac Patient with Penicillin Allergy)
        $patientUser1 = User::create([
            'name' => 'Ravi Kumar',
            'email' => 'patient@healthdesk.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'is_active' => true,
        ]);
        $p1 = Patient::create([
            'user_id' => $patientUser1->id,
            'mrn' => 'MRN-2026-0001',
            'first_name' => 'Ravi',
            'last_name' => 'Kumar',
            'date_of_birth' => '1988-06-14',
            'gender' => 'male',
            'phone' => '+91 9876543211',
            'email' => 'patient@healthdesk.com',
            'blood_group' => 'B+',
            'address' => 'Plot 42, Hitech City Main Road, Madhapur',
            'city' => 'Hyderabad',
            'state' => 'Telangana',
            'emergency_contact_name' => 'Sunita Kumar (Wife)',
            'emergency_contact_phone' => '+91 9876543999',
            'patient_status' => 'active',
            'pcp_name' => 'Dr. Ramesh Naidu (Internal Medicine)',
            'allergies' => 'Severe Penicillin Allergy (Anaphylaxis Risk), Shellfish',
            'medical_problems' => 'Essential Hypertension, Stage 1 Angina',
            'insurance_payer' => 'Aetna Medicare Advantage',
            'insurance_policy_no' => 'AET-9928192-01',
        ]);

        // 9. PATIENT 2: Sneha Patel (Diabetic Patient, Humana Medicare)
        $p2 = Patient::create([
            'mrn' => 'MRN-2026-0002',
            'first_name' => 'Sneha',
            'last_name' => 'Patel',
            'date_of_birth' => '1975-03-22',
            'gender' => 'female',
            'phone' => '+91 9988776655',
            'email' => 'sneha.patel@example.com',
            'blood_group' => 'O+',
            'address' => 'Flat 301, Silver Oak Apts, Banjara Hills Road No 12',
            'city' => 'Hyderabad',
            'state' => 'Telangana',
            'emergency_contact_name' => 'Amit Patel (Husband)',
            'emergency_contact_phone' => '+91 9988776600',
            'patient_status' => 'active',
            'pcp_name' => 'Dr. Priya Sharma',
            'allergies' => 'Aspirin (Mild Gastric Bleed Risk)',
            'medical_problems' => 'Type 2 Diabetes Mellitus, Hyperlipidemia',
            'insurance_payer' => 'Humana Medicare Advantage',
            'insurance_policy_no' => 'HUM-441209-X',
        ]);

        // 10. PATIENT 3: Master Aarav Varma (Child / Pediatric Patient)
        $p3 = Patient::create([
            'mrn' => 'MRN-2026-0003',
            'first_name' => 'Aarav',
            'last_name' => 'Varma',
            'date_of_birth' => '2019-11-10',
            'gender' => 'male',
            'phone' => '+91 9700112233',
            'email' => 'parent.varma@example.com',
            'blood_group' => 'A+',
            'address' => 'H.No 12-4-88, Gachibowli',
            'city' => 'Hyderabad',
            'state' => 'Telangana',
            'emergency_contact_name' => 'Kavita Varma (Mother)',
            'emergency_contact_phone' => '+91 9700112233',
            'patient_status' => 'active',
            'pcp_name' => 'Dr. Rajesh Varma',
            'allergies' => 'Peanut Allergy',
            'medical_problems' => 'Pediatric Bronchitis',
            'insurance_payer' => 'Blue Cross Blue Shield',
            'insurance_policy_no' => 'BCBS-781920',
        ]);

        // 11. PATIENT 4: Late Mr. Satyanarayana Murthy (Deceased Patient Demo)
        $p4 = Patient::create([
            'mrn' => 'MRN-2026-0004',
            'first_name' => 'Satyanarayana',
            'last_name' => 'Murthy',
            'date_of_birth' => '1942-01-15',
            'gender' => 'male',
            'phone' => '+91 9440011223',
            'email' => 'family.murthy@example.com',
            'blood_group' => 'AB+',
            'address' => 'Old City, Charminar',
            'city' => 'Hyderabad',
            'state' => 'Telangana',
            'patient_status' => 'deceased',
            'pcp_name' => 'Dr. Sairam Pulipati',
            'allergies' => 'None',
            'medical_problems' => 'Chronic Heart Failure (CHF)',
            'insurance_payer' => 'Medicare Traditional',
            'insurance_policy_no' => 'MED-110099',
        ]);

        // 12. APPOINTMENTS (Varied Real-world Scenarios)

        // Appt A: Ravi Kumar with Dr. Sairam (Ready in Exam Room, Vitals Recorded)
        $apptA = Appointment::create([
            'patient_id' => $p1->id,
            'doctor_id' => $doc1->id,
            'appointment_time' => now()->addMinutes(45),
            'status' => 'ready_for_doc',
            'chief_complaint' => 'Substernal chest tightness on exertion, mild shortness of breath.',
            'copay_collected' => 25.00,
        ]);
        $encA = Encounter::create([
            'appointment_id' => $apptA->id,
            'patient_id' => $p1->id,
            'doctor_id' => $doc1->id,
            'nurse_id' => $nurse1->id,
            'systolic_bp' => 138,
            'diastolic_bp' => 88,
            'pulse_rate' => 82,
            'temperature' => 98.6,
            'oxygen_spo2' => 98,
            'weight_kg' => 74.5,
            'subjective_notes' => 'Patient reports 4-day history of chest tightness after climbing stairs.',
            'objective_notes' => 'Cardiovascular S1/S2 present. Lungs clear bilaterally. ECG shows normal sinus rhythm.',
            'assessment' => 'Primary: I20.9 (Angina pectoris, unspecified). Secondary: I10 (Essential hypertension).',
            'clinical_plan' => 'Advised low sodium diet. Prescribed Amlodipine 5mg. Schedule 2D Echo.',
            'is_signed' => false,
        ]);

        // Appt B: Sneha Patel with Dr. Priya Sharma (Waiting in Triage Lobby)
        Appointment::create([
            'patient_id' => $p2->id,
            'doctor_id' => $doc2->id,
            'appointment_time' => now()->addHours(2),
            'status' => 'checked_in',
            'chief_complaint' => '3-month routine blood glucose review and fatigue.',
            'copay_collected' => 20.00,
        ]);

        // Appt C: Master Aarav with Dr. Rajesh Varma (Scheduled for Tomorrow)
        Appointment::create([
            'patient_id' => $p3->id,
            'doctor_id' => $doc3->id,
            'appointment_time' => now()->addDay()->setHour(10)->setMinute(30),
            'status' => 'scheduled',
            'chief_complaint' => 'Routine 5-year pediatric wellness exam and vaccination follow-up.',
            'copay_collected' => 0.00,
        ]);
    }
}
