<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Service extends Model
{
    // use SoftDeletes;
    

    // ==========================================
    // TABLE & PRIMARY KEY
    // ==========================================
    protected $table = 'services';
    protected $primaryKey = 'service_id';

    // ==========================================
    // FILLABLE - IMPROVED VERSION
    // ==========================================
    protected $fillable = [
        'code',
        'name',
        'category',
        'subcategory',
        'short_description',
        'description',
        
        // Pricing
        'price',
        'price_min',
        'price_max',
        'price_type',
        
        // Duration
        'duration_minutes',
        
        // Requirements
        'requirements',
        'requires_consultation',
        
        // Booking rules
        'is_bookable_online',
        'is_series_treatment',
        'estimated_sessions',
        
        // SEO
        'slug',
        'meta_title',
        'meta_description',
        
        // Media
        'icon',
        'image_path',
        'gallery_images',
        
        // Display
        'display_order',
        'is_featured',
        'is_popular',
        'is_active',
        
        // Statistics
        'total_bookings',
        'last_booked_at',
    ];

    // ==========================================
    // CASTS
    // ==========================================
    protected $casts = [
        'price' => 'decimal:2',
        'price_min' => 'decimal:2',
        'price_max' => 'decimal:2',
        'duration_minutes' => 'integer',
        'estimated_sessions' => 'integer',
        'display_order' => 'integer',
        'total_bookings' => 'integer',
        
        'requires_consultation' => 'boolean',
        'is_bookable_online' => 'boolean',
        'is_series_treatment' => 'boolean',
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        
        'gallery_images' => 'array',
        'last_booked_at' => 'datetime',
    ];

    // ==========================================
    // APPENDS
    // ==========================================
    protected $appends = [
        'formatted_price',
        'formatted_duration',
        'image_url',
        'detail_url',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'service_id', 'service_id');
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'service_id', 'service_id');
    }

    public function visitDetails()
    {
        return $this->hasMany(VisitDetail::class, 'service_id', 'service_id');
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getFormattedPriceAttribute()
    {
        if ($this->price_type === 'contact') {
            return 'Hubungi Kami';
        }

        if ($this->price_type === 'range') {
            $min = number_format($this->price_min, 0, ',', '.');
            $max = number_format($this->price_max, 0, ',', '.');
            return "Rp {$min} - Rp {$max}";
        }

        // Fixed price
        if (!$this->price || $this->price == 0) {
            return 'Hubungi Kami';
        }

        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedDurationAttribute()
    {
        $minutes = $this->duration_minutes ?? 30;
        
        if ($minutes >= 60) {
            $hours = floor($minutes / 60);
            $mins = $minutes % 60;
            return $mins > 0 ? "{$hours} jam {$mins} menit" : "{$hours} jam";
        }
        
        return "{$minutes} menit";
    }

    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }

        return null;
    }

    public function getDetailUrlAttribute()
    {
        if ($this->slug) {
            return route('landing.service.detail', $this->slug);
        }

        return route('landing.service.detail', $this->service_id);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    public function scopeForLanding($query, $limit = 6)
    {
        return $query->active()->ordered()->limit($limit);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeBookable($query)
    {
        return $query->where('is_bookable_online', true);
    }

    // ==========================================
    // METHODS
    // ==========================================

    public static function getCategories()
    {
        return static::whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();
    }

    public function incrementBookingCount()
    {
        $this->increment('total_bookings');
        $this->update(['last_booked_at' => now()]);
    }

    // ==========================================
    // BOOT - Auto Slug Generation
    // ==========================================

    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from name
        static::creating(function ($service) {
            if (!$service->slug) {
                $service->slug = Str::slug($service->name);
            }
        });

        // Validation
        static::saving(function ($service) {
            if ($service->duration_minutes <= 0) {
                throw new \Exception('Duration must be greater than 0');
            }

            if ($service->price < 0) {
                throw new \Exception('Price cannot be negative');
            }
        });
    }
}
