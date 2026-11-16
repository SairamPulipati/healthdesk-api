<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encounters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->unique()->constrained('appointments')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            
            // Nurse Triage Vitals
            $table->foreignId('nurse_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('systolic_bp')->nullable();
            $table->integer('diastolic_bp')->nullable();
            $table->integer('pulse_rate')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->integer('oxygen_spo2')->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            
            // Doctor Clinical SOAP Notes (OpenEMR Standard)
            $table->text('subjective_notes')->nullable(); // History of Present Illness
            $table->text('objective_notes')->nullable();  // Physical Exam
            $table->text('assessment')->nullable();       // ICD-10 Diagnosis Summary
            $table->text('clinical_plan')->nullable();    // Treatment & Orders
            
            // Lock & Digital Signature
            $table->boolean('is_signed')->default(false);
            $table->dateTime('signed_at')->nullable();
            $table->string('signed_by_doctor')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encounters');
    }
};
