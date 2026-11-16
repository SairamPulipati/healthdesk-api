<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('appointment_time');
            $table->enum('status', [
                'scheduled',
                'checked_in',
                'in_triage',
                'ready_for_doc',
                'in_consultation',
                'completed',
                'cancelled'
            ])->default('scheduled');
            $table->text('chief_complaint')->nullable();
            $table->decimal('copay_collected', 8, 2)->default(0.00);
            $table->timestamps();

            $table->index(['doctor_id', 'appointment_time']);
            $table->index(['patient_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
