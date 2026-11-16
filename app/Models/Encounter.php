<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encounter extends Model
{
    protected $fillable = [
        'appointment_id',
        'patient_id',
        'doctor_id',
        'nurse_id',
        'systolic_bp',
        'diastolic_bp',
        'pulse_rate',
        'temperature',
        'oxygen_spo2',
        'weight_kg',
        'subjective_notes',
        'objective_notes',
        'assessment',
        'clinical_plan',
        'is_signed',
        'signed_at',
        'signed_by_doctor',
    ];

    protected $casts = [
        'is_signed' => 'boolean',
        'signed_at' => 'datetime',
        'temperature' => 'float',
        'weight_kg' => 'float',
    ];

    public function appointment() { return $this->belongsTo(Appointment::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function doctor() { return $this->belongsTo(User::class, 'doctor_id'); }
    public function nurse() { return $this->belongsTo(User::class, 'nurse_id'); }
    public function prescriptions() { return $this->hasMany(Prescription::class); }
}
