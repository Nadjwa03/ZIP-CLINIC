<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'status',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the doctor profile associated with this user
     */
    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'doctor_user_id', 'id');
    }

    /**
     * Get all patient profiles owned by this user (bisa punya banyak - keluarga)
     */
    public function patients()
    {
        return $this->hasMany(Patient::class, 'owner_user_id', 'id');  // ✅ BENAR
    }

    /**
     * Get the primary/first patient profile (untuk backward compatibility)
     */
    public function patient()
    {
        return $this->hasOne(Patient::class, 'owner_user_id', 'id');  // ✅ BENAR
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is doctor
     */
    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    /**
     * Check if user is patient
     */
    public function isPatient(): bool
    {
        return $this->role === 'patient';
    }
}