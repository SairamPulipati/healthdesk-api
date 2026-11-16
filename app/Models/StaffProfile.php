<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffProfile extends Model
{
    protected $fillable = [
        'user_id',
        'employee_code',
        'department',
        'specialization',
        'consultation_fee',
        'phone',
        'date_of_birth',
        'address',
        'city',
        'is_on_duty',
        'employment_status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_on_duty' => 'boolean',
        'consultation_fee' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
