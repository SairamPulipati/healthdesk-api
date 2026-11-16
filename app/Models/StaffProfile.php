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
        'is_on_duty',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
