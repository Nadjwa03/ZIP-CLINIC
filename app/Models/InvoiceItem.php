<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    // ==========================================
    // TABLE & PRIMARY KEY
    // ==========================================
    protected $table = 'invoice_items';
    protected $primaryKey = 'invoice_item_id'; // ✅ PENTING!

    // ==========================================
    // ITEM TYPE CONSTANTS
    // ==========================================
    const TYPE_SERVICE = 'SERVICE';
    const TYPE_PRODUCT = 'PRODUCT';

    // ==========================================
    // FILLABLE - SESUAI MIGRATION
    // ==========================================
    protected $fillable = [
        'invoice_id',
        'item_type',
        'service_id',
        'inventory_item_id',  // ✅ For PRODUCT type
        'name',
        'unit',
        'qty',
        'unit_price',
        'discount',
        'subtotal',           // Auto-calculated or manual
        'tooth_codes',
        'notes',
    ];

    // ==========================================
    // CASTS
    // ==========================================
    protected $casts = [
        'qty' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // ==========================================
    // APPENDS - Virtual Attributes
    // ==========================================
    protected $appends = [
        'gross_total',
        'net_total',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Invoice yang memiliki item ini
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }

    /**
     * Service reference (untuk item_type = SERVICE)
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    /**
     * Inventory item reference (untuk item_type = PRODUCT)
     */
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id', 'inventory_item_id');
    }

    // ==========================================
    // ACCESSORS - Virtual Attributes
    // ==========================================

    /**
     * Get gross total (qty × unit_price sebelum diskon)
     */
    public function getGrossTotalAttribute()
    {
        return $this->qty * $this->unit_price;
    }

    /**
     * Get net total (gross_total - discount)
     */
    public function getNetTotalAttribute()
    {
        return $this->gross_total - $this->discount;
    }

    /**
     * Check if this is a service item
     */
    public function getIsServiceAttribute()
    {
        return $this->item_type === self::TYPE_SERVICE;
    }

    /**
     * Check if this is a product item
     */
    public function getIsProductAttribute()
    {
        return $this->item_type === self::TYPE_PRODUCT;
    }

    /**
     * Get formatted tooth codes (untuk display)
     */
    public function getFormattedToothCodesAttribute()
    {
        if (!$this->tooth_codes) {
            return null;
        }

        // Convert "11,12,21" → "Gigi 11, 12, 21"
        $codes = explode(',', $this->tooth_codes);
        $codes = array_map('trim', $codes);
        
        return 'Gigi ' . implode(', ', $codes);
    }

    // ==========================================
    // MUTATORS
    // ==========================================

    /**
     * Auto-calculate subtotal before saving
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-calculate subtotal saat saving
        static::saving(function ($item) {
            if (!$item->subtotal || $item->isDirty(['qty', 'unit_price', 'discount'])) {
                $item->subtotal = ($item->qty * $item->unit_price) - $item->discount;
            }
        });
    }

    // ==========================================
    // METHODS - Factory Methods
    // ==========================================

    /**
     * Create invoice item from service
     */
    public static function createFromService(Service $service, $qty = 1, $discount = 0, $toothCodes = null)
    {
        $unitPrice = $service->price ?? 0;
        $subtotal = ($qty * $unitPrice) - $discount;
        
        return new static([
            'item_type' => self::TYPE_SERVICE,
            'service_id' => $service->service_id, // ✅ Benar
            'inventory_item_id' => null,
            'name' => $service->name,
            'unit' => 'Tindakan',
            'qty' => $qty,
            'unit_price' => $unitPrice,
            'discount' => $discount,
            'subtotal' => $subtotal,
            'tooth_codes' => $toothCodes,
        ]);
    }

    /**
     * Create invoice item from product/inventory
     */
    public static function createFromProduct(InventoryItem $product, $qty = 1, $discount = 0)
    {
        $unitPrice = $product->sell_price ?? 0;
        $subtotal = ($qty * $unitPrice) - $discount;
        
        return new static([
            'item_type' => self::TYPE_PRODUCT,
            'service_id' => null,
            'inventory_item_id' => $product->inventory_item_id,
            'name' => $product->name,
            'unit' => $product->unit ?? 'Pcs',
            'qty' => $qty,
            'unit_price' => $unitPrice,
            'discount' => $discount,
            'subtotal' => $subtotal,
        ]);
    }

    /**
     * Create invoice item from visit detail (treatment record)
     */
    public static function createFromVisitDetail($visitDetail)
    {
        $service = $visitDetail->service;
        
        return new static([
            'item_type' => self::TYPE_SERVICE,
            'service_id' => $service?->service_id,
            'inventory_item_id' => null,
            'name' => $service?->name ?? $visitDetail->treatment_note ?? 'Tindakan',
            'unit' => 'Tindakan',
            'qty' => 1,
            'unit_price' => $service?->price ?? 0,
            'discount' => 0,
            'subtotal' => $service?->price ?? 0,
            'tooth_codes' => $visitDetail->tooth_codes,
            'notes' => $visitDetail->diagnosis_note,
        ]);
    }

    // ==========================================
    // METHODS - Calculations
    // ==========================================

    /**
     * Calculate and update subtotal
     */
    public function calculateSubtotal()
    {
        $this->subtotal = ($this->qty * $this->unit_price) - $this->discount;
        return $this;
    }

    /**
     * Apply discount (amount or percentage)
     */
    public function applyDiscount($value, $isPercentage = false)
    {
        if ($isPercentage) {
            // Percentage discount
            $this->discount = ($this->qty * $this->unit_price) * ($value / 100);
        } else {
            // Fixed amount discount
            $this->discount = $value;
        }

        $this->calculateSubtotal();
        
        return $this;
    }

    /**
     * Update quantity and recalculate
     */
    public function updateQuantity($qty)
    {
        $this->qty = $qty;
        $this->calculateSubtotal();
        
        return $this;
    }

    /**
     * Update price and recalculate
     */
    public function updatePrice($unitPrice)
    {
        $this->unit_price = $unitPrice;
        $this->calculateSubtotal();
        
        return $this;
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope untuk service items
     */
    public function scopeServices($query)
    {
        return $query->where('item_type', self::TYPE_SERVICE);
    }

    /**
     * Scope untuk product items
     */
    public function scopeProducts($query)
    {
        return $query->where('item_type', self::TYPE_PRODUCT);
    }

    /**
     * Scope untuk items dengan tooth codes (dental treatments)
     */
    public function scopeWithToothCodes($query)
    {
        return $query->whereNotNull('tooth_codes');
    }
}
