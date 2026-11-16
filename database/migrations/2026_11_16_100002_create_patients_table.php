<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('mrn')->unique(); // Medical Record Number e.g. PAT-2026-001
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->default('other');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->enum('patient_status', ['active', 'inactive', 'transferred', 'deceased'])->default('active');
            $table->string('pcp_name')->nullable(); // Primary Care Physician
            $table->text('allergies')->nullable(); // Triggers PR #1930 Allergy Alert
            $table->text('medical_problems')->nullable(); // Chronic conditions e.g. Hypertension, Diabetes
            $table->string('insurance_payer')->default('Self Pay'); // Aetna, Humana, Medicare
            $table->string('insurance_policy_no')->nullable();
            $table->timestamps();

            $table->index(['mrn', 'phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
