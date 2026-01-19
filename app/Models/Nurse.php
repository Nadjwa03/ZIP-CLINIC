<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nurse extends Model
{
    protected $table = 'nurses';
    protected $primaryKey = 'nurse_user_id';
    public $incrementing = false; // Karena PK adalah FK ke users

    protected $fillable = [
        'nurse_user_id',
        'name',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Get the user account for this nurse
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'nurse_user_id', 'id');
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    /**
     * Get email from user relationship
     */
    public function getEmailAttribute()
    {
        return $this->user->email ?? '';
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope to get only active nurses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}