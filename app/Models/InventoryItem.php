<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'inventory_items';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'inventory_item_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'SKU',
        'name',
        'description',
        'type',
        'category',
        'purchase_price',
        'sell_price',
        'markup_percentage',
        'unit',
        'qty_on_hand',
        'min_stock',
        'max_stock',
        'vendor_name',
        'vendor_phone',
        'medicine_type',
        'dosage',
        'expiry_date',
        'batch_number',
        'is_active',
        'is_prescription_required',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'markup_percentage' => 'decimal:2',
        'qty_on_hand' => 'integer',
        'min_stock' => 'integer',
        'max_stock' => 'integer',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
        'is_prescription_required' => 'boolean',
    ];

    /**
     * Item Type Constants
     */
    const TYPE_MEDICINE = 'MEDICINE';
    const TYPE_EQUIPMENT = 'EQUIPMENT';
    const TYPE_CONSUMABLE = 'CONSUMABLE';
    const TYPE_OTHER = 'OTHER';

    // ====================================
    // RELATIONSHIPS
    // ====================================

    /**
     * Get invoice items that use this inventory item
     */
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'inventory_item_id', 'inventory_item_id');
    }

    // ====================================
    // ACCESSORS & MUTATORS
    // ====================================

    /**
     * Get formatted type label
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            self::TYPE_MEDICINE => 'Obat',
            self::TYPE_EQUIPMENT => 'Alat Medis',
            self::TYPE_CONSUMABLE => 'Habis Pakai',
            self::TYPE_OTHER => 'Lainnya',
            default => $this->type,
        };
    }

    /**
     * Get stock status
     */
    public function getStockStatusAttribute()
    {
        if ($this->qty_on_hand <= 0) {
            return 'OUT_OF_STOCK';
        } elseif ($this->qty_on_hand <= $this->min_stock) {
            return 'LOW_STOCK';
        } else {
            return 'IN_STOCK';
        }
    }

    /**
     * Get stock status label
     */
    public function getStockStatusLabelAttribute()
    {
        return match($this->stock_status) {
            'OUT_OF_STOCK' => 'Habis',
            'LOW_STOCK' => 'Stok Rendah',
            'IN_STOCK' => 'Tersedia',
            default => 'Unknown',
        };
    }

    /**
     * Get stock status color
     */
    public function getStockStatusColorAttribute()
    {
        return match($this->stock_status) {
            'OUT_OF_STOCK' => 'red',
            'LOW_STOCK' => 'yellow',
            'IN_STOCK' => 'green',
            default => 'gray',
        };
    }

    /**
     * Get total asset value for this item
     */
    public function getAssetValueAttribute()
    {
        return $this->qty_on_hand * $this->purchase_price;
    }

    /**
     * Check if item is expired
     */
    public function getIsExpiredAttribute()
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->isPast();
    }

    /**
     * Check if item is near expiry (within 30 days)
     */
    public function getIsNearExpiryAttribute()
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->isFuture() && $this->expiry_date->diffInDays(now()) <= 30;
    }

    /**
     * Get profit margin
     */
    public function getProfitMarginAttribute()
    {
        if ($this->purchase_price == 0) {
            return 0;
        }

        return $this->sell_price - $this->purchase_price;
    }

    /**
     * Get profit margin percentage
     */
    public function getProfitMarginPercentageAttribute()
    {
        if ($this->purchase_price == 0) {
            return 0;
        }

        return (($this->sell_price - $this->purchase_price) / $this->purchase_price) * 100;
    }

    // ====================================
    // SCOPES
    // ====================================

    /**
     * Scope: Active items only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Items by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Medicines only
     */
    public function scopeMedicines($query)
    {
        return $query->where('type', self::TYPE_MEDICINE);
    }

    /**
     * Scope: Equipment only
     */
    public function scopeEquipment($query)
    {
        return $query->where('type', self::TYPE_EQUIPMENT);
    }

    /**
     * Scope: Items in stock
     */
    public function scopeInStock($query)
    {
        return $query->where('qty_on_hand', '>', 0);
    }

    /**
     * Scope: Items with low stock
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('qty_on_hand', '<=', 'min_stock')
                     ->where('qty_on_hand', '>', 0);
    }

    /**
     * Scope: Out of stock items
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('qty_on_hand', '<=', 0);
    }

    /**
     * Scope: Expired items
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
                     ->whereDate('expiry_date', '<', now());
    }

    /**
     * Scope: Items near expiry (within 30 days)
     */
    public function scopeNearExpiry($query)
    {
        return $query->whereNotNull('expiry_date')
                     ->whereDate('expiry_date', '>=', now())
                     ->whereDate('expiry_date', '<=', now()->addDays(30));
    }

    /**
     * Scope: Search by name or SKU
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('SKU', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // ====================================
    // STOCK MANAGEMENT METHODS
    // ====================================

    /**
     * Add stock quantity
     */
    public function addStock($quantity, $reason = null)
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive');
        }

        $this->qty_on_hand += $quantity;
        $this->save();

        return $this;
    }

    /**
     * Reduce stock quantity
     */
    public function reduceStock($quantity, $reason = null)
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive');
        }

        if ($this->qty_on_hand < $quantity) {
            throw new \Exception("Insufficient stock. Available: {$this->qty_on_hand}, Requested: {$quantity}");
        }

        $this->qty_on_hand -= $quantity;
        $this->save();

        return $this;
    }

    /**
     * Check if sufficient stock is available
     */
    public function hasStock($quantity)
    {
        return $this->qty_on_hand >= $quantity;
    }

    /**
     * Set stock to specific amount
     */
    public function setStock($quantity, $reason = null)
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity cannot be negative');
        }

        $this->qty_on_hand = $quantity;
        $this->save();

        return $this;
    }

    // ====================================
    // STATIC METHODS
    // ====================================

    /**
     * Generate unique SKU
     */
    public static function generateSKU($prefix = 'INV')
    {
        do {
            $sku = $prefix . '-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('SKU', $sku)->exists());

        return $sku;
    }

    /**
     * Get inventory statistics
     */
    public static function getStatistics()
    {
        $totalItems = self::active()->count();
        $totalValue = self::active()->sum(\DB::raw('qty_on_hand * purchase_price'));
        $inStock = self::active()->whereColumn('qty_on_hand', '>', 'min_stock')->count();
        $lowStock = self::active()->lowStock()->count();
        $outOfStock = self::active()->outOfStock()->count();
        $expired = self::active()->expired()->count();
        $nearExpiry = self::active()->nearExpiry()->count();

        return [
            'total_items' => $totalItems,
            'total_value' => $totalValue,
            'in_stock' => $inStock,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'expired' => $expired,
            'near_expiry' => $nearExpiry,
        ];
    }
}
