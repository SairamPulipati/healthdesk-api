<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isDoctor(): bool { return $this->role === 'doctor'; }
    public function isNurse(): bool { return $this->role === 'nurse'; }
    public function isReceptionist(): bool { return $this->role === 'receptionist'; }
    public function isPatient(): bool { return $this->role === 'patient'; }

    public function staffProfile()
    {
        return $this->hasOne(StaffProfile::class);
    }

    public function patientProfile()
    {
        return $this->hasOne(Patient::class);
    }
}
