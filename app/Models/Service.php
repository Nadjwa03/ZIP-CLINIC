<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';
    protected $primaryKey = 'service_id';

    protected $fillable = [
        'code',
        'service_name',
        'speciality_id',
        'description',
        'price',
        'duration_minutes',
        'is_active',
        'is_public',
        'category',
        'display_order',
        'icon',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'duration_minutes' => 'integer',
        'display_order' => 'integer',
    ];

    // scope yang dipakai LandingController (sekarang ordered() belum ada)
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('service_name');
    }

    /**
     * Get the speciality of this service
     */
    public function speciality()
    {
        return $this->belongsTo(Speciality::class, 'speciality_id', 'speciality_id');
    }

    /**
     * Get appointments using this service
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'service_id', 'service_id');
    }

    /**
     * Get invoice items using this service
     */
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'service_id', 'service_id');
    }

    /**
     * Scope: Active services only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Search by name or description
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('service_name', 'like', '%' . $term . '%')
              ->orWhere('description', 'like', '%' . $term . '%')
              ->orWhere('code', 'like', '%' . $term . '%');
        });
    }

    /**
     * Get formatted price in Rupiah
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get duration in human readable format
     */
    public function getFormattedDurationAttribute()
    {
        if ($this->duration_minutes < 60) {
            return $this->duration_minutes . ' menit';
        } else {
            $hours = floor($this->duration_minutes / 60);
            $minutes = $this->duration_minutes % 60;

            if ($minutes > 0) {
                return $hours . ' jam ' . $minutes . ' menit';
            } else {
                return $hours . ' jam';
            }
        }
    }
}
