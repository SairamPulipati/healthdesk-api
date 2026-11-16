<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_time',
        'status',
        'chief_complaint',
        'copay_collected',
    ];

    protected $casts = [
        'appointment_time' => 'datetime',
        'copay_collected' => 'float',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function encounter()
    {
        return $this->hasOne(Encounter::class);
    }
}
