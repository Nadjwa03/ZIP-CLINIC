<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id('inventory_item_id');

            // Basic Product Info
            $table->string('SKU', 40)->unique()->comment('Stock Keeping Unit');
            $table->string('name', 160)->comment('Product/medicine name');
            $table->text('description')->nullable()->comment('Product description');

            // Classification
            $table->enum('type', ['MEDICINE', 'EQUIPMENT', 'CONSUMABLE', 'OTHER'])->default('MEDICINE');
            $table->string('category', 50)->nullable()->comment('Product category');

            // Pricing
            $table->decimal('purchase_price', 12, 2)->default(0)->comment('Buy price from vendor');
            $table->decimal('sell_price', 12, 2)->default(0)->comment('Sell price to patient');
            $table->decimal('markup_percentage', 5, 2)->nullable()->comment('Profit margin %');

            // Stock Management
            $table->string('unit', 20)->default('pcs')->comment('Unit of measurement');
            $table->integer('qty_on_hand')->default(0)->comment('Current stock quantity');
            $table->integer('min_stock')->default(10)->comment('Minimum stock alert threshold');
            $table->integer('max_stock')->nullable()->comment('Maximum stock capacity');

            // Vendor/Supplier
            $table->string('vendor_name', 100)->nullable()->comment('Supplier name');
            $table->string('vendor_phone', 20)->nullable();

            // Medicine-specific fields
            $table->string('medicine_type', 50)->nullable()->comment('e.g., Tablet, Syrup, Injection');
            $table->string('dosage', 50)->nullable()->comment('e.g., 500mg, 10ml');
            $table->date('expiry_date')->nullable()->comment('Expiration date');
            $table->string('batch_number', 50)->nullable()->comment('Batch/Lot number');

            // Status & Notes
            $table->boolean('is_active')->default(true);
            $table->boolean('is_prescription_required')->default(false)->comment('Require doctor prescription');
            $table->text('notes')->nullable()->comment('Additional notes');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('SKU');
            $table->index('name');
            $table->index('type');
            $table->index('category');
            $table->index(['is_active', 'qty_on_hand']);
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
