<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'mrn',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'blood_group',
        'address',
        'city',
        'state',
        'emergency_contact_name',
        'emergency_contact_phone',
        'patient_status',
        'pcp_name',
        'allergies',
        'medical_problems',
        'insurance_payer',
        'insurance_policy_no',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function encounters()
    {
        return $this->hasMany(Encounter::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
