<?php

namespace App\Http\Livewire\Admin\Inventory;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\InventoryItem;

class InventoryIndex extends Component
{
    use WithPagination;
    
    public $searchTerm = '';
    public $categoryFilter = 'all';
    public $statusFilter = 'all';
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    
    // Modal states
    public $showAddModal = false;
    public $showEditModal = false;
    public $showOrderModal = false;
    
    // Form fields
    public $itemId;
    public $SKU;
    public $name;
    public $description;
    public $type = 'MEDICINE';
    public $unit = 'pcs';
    public $category;
    public $purchase_price = 0;
    public $sell_price = 0;
    public $qty_on_hand = 0;
    public $min_stock = 10;
    public $vendor_name;
    public $is_active = true;
    
    // Statistics
    public $totalValue = 0;
    public $totalProducts = 0;
    public $inStock = 0;
    public $lowStock = 0;
    public $outOfStock = 0;
    
    protected $paginationTheme = 'tailwind';
    
    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'categoryFilter' => ['except' => 'all'],
        'statusFilter' => ['except' => 'all']
    ];
    
    protected function rules()
    {
        return [
            'SKU' => 'required|string|max:40|unique:inventory_items,SKU,' . $this->itemId . ',inventory_item_id',
            'name' => 'required|string|max:160',
            'description' => 'nullable|string',
            'type' => 'required|in:MEDICINE,EQUIPMENT,CONSUMABLE,OTHER',
            'unit' => 'required|string|max:20',
            'category' => 'nullable|string|max:50',
            'purchase_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'qty_on_hand' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'vendor_name' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ];
    }
    
    public function render()
    {
        $items = $this->getInventoryItems();
        
        // Calculate statistics
        $this->calculateStats();
        
        // Get unique categories
        $categories = InventoryItem::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');
        
        return view('livewire.admin.inventory.inventory-index', [
            'items' => $items,
            'categories' => $categories,
        ]);
    }
    
    public function getInventoryItems()
    {
        $query = InventoryItem::query();
        
        // Search
        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('SKU', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('vendor_name', 'like', '%' . $this->searchTerm . '%');
            });
        }
        
        // Category filter
        if ($this->categoryFilter !== 'all') {
            $query->where('category', $this->categoryFilter);
        }
        
        // Status filter
        if ($this->statusFilter === 'in_stock') {
            $query->where('qty_on_hand', '>', 'min_stock');
        } elseif ($this->statusFilter === 'low_stock') {
            $query->where('qty_on_hand', '<=', 'min_stock')
                  ->where('qty_on_hand', '>', 0);
        } elseif ($this->statusFilter === 'out_of_stock') {
            $query->where('qty_on_hand', '=', 0);
        }
        
        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);
        
        return $query->paginate(15);
    }
    
    public function calculateStats()
    {
        // Optimize: Use query builder instead of loading all items
        $this->totalProducts = InventoryItem::active()->count();
        $this->totalValue = InventoryItem::active()->sum(\DB::raw('qty_on_hand * purchase_price'));
        $this->inStock = InventoryItem::active()->where('qty_on_hand', '>', 0)->count();
        $this->lowStock = InventoryItem::active()->whereColumn('qty_on_hand', '<=', 'min_stock')->where('qty_on_hand', '>', 0)->count();
        $this->outOfStock = InventoryItem::active()->where('qty_on_hand', '=', 0)->count();
    }
    
    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }
    
    public function openAddModal()
    {
        $this->resetForm();
        $this->showAddModal = true;
    }
    
    public function openEditModal($itemId)
    {
        $item = InventoryItem::findOrFail($itemId);

        $this->itemId = $item->inventory_item_id;
        $this->SKU = $item->SKU;
        $this->name = $item->name;
        $this->description = $item->description;
        $this->type = $item->type;
        $this->unit = $item->unit;
        $this->category = $item->category;
        $this->purchase_price = $item->purchase_price;
        $this->sell_price = $item->sell_price;
        $this->qty_on_hand = $item->qty_on_hand;
        $this->min_stock = $item->min_stock;
        $this->vendor_name = $item->vendor_name;
        $this->is_active = $item->is_active;

        $this->showEditModal = true;
    }
    
    public function saveItem()
    {
        $this->validate();

        InventoryItem::create([
            'SKU' => $this->SKU,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'unit' => $this->unit,
            'category' => $this->category,
            'purchase_price' => $this->purchase_price,
            'sell_price' => $this->sell_price,
            'qty_on_hand' => $this->qty_on_hand,
            'min_stock' => $this->min_stock,
            'vendor_name' => $this->vendor_name,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Item berhasil ditambahkan');
        $this->showAddModal = false;
        $this->resetForm();
    }
    
    public function updateItem()
    {
        $this->validate();

        $item = InventoryItem::findOrFail($this->itemId);
        $item->update([
            'SKU' => $this->SKU,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'unit' => $this->unit,
            'category' => $this->category,
            'purchase_price' => $this->purchase_price,
            'sell_price' => $this->sell_price,
            'qty_on_hand' => $this->qty_on_hand,
            'min_stock' => $this->min_stock,
            'vendor_name' => $this->vendor_name,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Item berhasil diupdate');
        $this->showEditModal = false;
        $this->resetForm();
    }
    
    public function deleteItem($itemId)
    {
        InventoryItem::findOrFail($itemId)->delete();
        session()->flash('success', 'Item berhasil dihapus');
    }
    
    public function resetForm()
    {
        $this->reset([
            'itemId', 'SKU', 'name', 'unit', 'category', 
            'sell_price', 'qty_on_hand', 'min_stock', 'vendor_name'
        ]);
        $this->is_active = true;
        $this->resetErrorBag();
    }
    
    public function getStockStatus($item)
    {
        if ($item->qty_on_hand == 0) {
            return ['label' => 'OUT OF STOCK', 'color' => 'text-red-600 bg-red-50'];
        } elseif ($item->qty_on_hand <= $item->min_stock) {
            return ['label' => 'LOW STOCK', 'color' => 'text-orange-600 bg-orange-50'];
        } else {
            return ['label' => 'IN STOCK', 'color' => 'text-green-600 bg-green-50'];
        }
    }
}