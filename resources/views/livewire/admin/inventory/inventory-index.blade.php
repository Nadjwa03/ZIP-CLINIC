@extends('layouts.admin')

@section('title', 'Inventory - Klinik ZIP')

@section('content')
    @livewire('admin.inventory.inventory-index')
@endsection
<div class="px-6 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Stocks</h1>
        <p class="text-gray-600 text-sm mt-1">Manage inventory and stock levels</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center mb-2">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 mb-1">TOTAL ASSET VALUE</p>
            <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalValue, 0, ',', '.') }}</p>
            <div class="mt-3 flex items-center space-x-2">
                <div class="flex-1 bg-teal-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-teal-500 h-full" style="width: 65%"></div>
                </div>
            </div>
            <div class="mt-2 flex items-center justify-between text-xs">
                <span class="text-teal-600 font-medium">● In stock: {{ $inStock }}</span>
                <span class="text-orange-600 font-medium">● Low stock: {{ $lowStock }}</span>
                <span class="text-red-600 font-medium">● Out of stock: {{ $outOfStock }}</span>
            </div>
        </div>
        
        <div class="bg-teal-50 rounded-lg shadow-sm border border-teal-100 p-6">
            <p class="text-sm text-teal-600 font-medium mb-1">{{ $totalProducts }} product</p>
            <p class="text-xs text-teal-500 mb-3">In stock</p>
            <div class="flex items-center justify-center">
                <div class="relative w-24 h-24">
                    <svg class="transform -rotate-90" width="96" height="96">
                        <circle cx="48" cy="48" r="40" stroke="#d1fae5" stroke-width="8" fill="none"/>
                        <circle cx="48" cy="48" r="40" stroke="#10b981" stroke-width="8" fill="none"
                                stroke-dasharray="{{ 2 * 3.14159 * 40 * ($inStock / max($totalProducts, 1)) }} {{ 2 * 3.14159 * 40 }}"
                                stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-2xl font-bold text-teal-600">{{ $inStock }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-orange-50 rounded-lg shadow-sm border border-orange-100 p-6">
            <p class="text-sm text-orange-600 font-medium mb-1">{{ $lowStock }} product</p>
            <p class="text-xs text-orange-500 mb-3">Low stock</p>
            <div class="flex items-center justify-center">
                <div class="relative w-24 h-24">
                    <svg class="transform -rotate-90" width="96" height="96">
                        <circle cx="48" cy="48" r="40" stroke="#fed7aa" stroke-width="8" fill="none"/>
                        <circle cx="48" cy="48" r="40" stroke="#f97316" stroke-width="8" fill="none"
                                stroke-dasharray="{{ 2 * 3.14159 * 40 * ($lowStock / max($totalProducts, 1)) }} {{ 2 * 3.14159 * 40 }}"
                                stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-2xl font-bold text-orange-600">{{ $lowStock }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-red-50 rounded-lg shadow-sm border border-red-100 p-6">
            <p class="text-sm text-red-600 font-medium mb-1">{{ $outOfStock }} product</p>
            <p class="text-xs text-red-500 mb-3">Out of stock</p>
            <div class="flex items-center justify-center">
                <div class="relative w-24 h-24">
                    <svg class="transform -rotate-90" width="96" height="96">
                        <circle cx="48" cy="48" r="40" stroke="#fecaca" stroke-width="8" fill="none"/>
                        <circle cx="48" cy="48" r="40" stroke="#ef4444" stroke-width="8" fill="none"
                                stroke-dasharray="{{ 2 * 3.14159 * 40 * ($outOfStock / max($totalProducts, 1)) }} {{ 2 * 3.14159 * 40 }}"
                                stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-2xl font-bold text-red-600">{{ $outOfStock }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button class="border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600">
                Inventory
            </button>
            <button class="border-transparent hover:border-gray-300 py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                Order Stock
            </button>
        </nav>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <!-- Search -->
                <div class="relative">
                    <input type="text" 
                           wire:model.debounce.300ms="searchTerm"
                           placeholder="Search name or reservation ID..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm w-80 focus:ring-blue-500 focus:border-blue-500">
                    <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                
                <!-- Filters Button -->
                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    Filters
                </button>
            </div>
            
            <div class="flex items-center space-x-3">
                <!-- Status Filter -->
                <select wire:model="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Status</option>
                    <option value="in_stock">In Stock</option>
                    <option value="low_stock">Low Stock</option>
                    <option value="out_of_stock">Out of Stock</option>
                </select>
                
                <!-- Order Stock Button -->
                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Order Stock
                </button>
                
                <!-- Add New Product -->
                <button wire:click="openAddModal" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Product
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Inventory Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th wire:click="sortBy('name')" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            <div class="flex items-center">
                                NAME
                                @if($sortBy === 'name')
                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                        @if($sortDirection === 'asc')
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                        @else
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        @endif
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">CATEGORIES</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">VENDOR</th>
                        <th wire:click="sortBy('qty_on_hand')" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            STOCK
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">STATUS</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ASSET VALUE</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($items as $item)
                        @php
                            $stockStatus = $this->getStockStatus($item);
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $item->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $item->unit }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $item->category ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ $item->SKU }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="text-sm text-gray-600">{{ $item->vendor_name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ $item->qty_on_hand }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $stockStatus['color'] }}">
                                    ● {{ $stockStatus['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900">
                                    Rp {{ number_format($item->qty_on_hand * $item->sell_price, 0, ',', '.') }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="openEditModal({{ $item->inventory_item_id }})" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <p class="text-gray-600 font-medium">No inventory items found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $items->links() }}
        </div>
    </div>
</div>