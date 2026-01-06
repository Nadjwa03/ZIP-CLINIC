<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop table dulu jika ada (untuk avoid conflict)
        Schema::dropIfExists('services');

        Schema::create('services', function (Blueprint $table) {
            $table->id('service_id');
            $table->string('code', 50)->nullable()->unique();
            $table->string('service_name', 200);
            $table->unsignedBigInteger('speciality_id');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2);
            $table->integer('duration_minutes');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true)->comment('Can be booked by patients online');
            $table->string('category', 50)->nullable()->comment('Service category for grouping');
            $table->integer('display_order')->default(0)->comment('Order for display (lower = first)');
            $table->string('icon', 50)->nullable()->comment('Icon/emoji for category');
            $table->timestamps();

            // Foreign Keys
            $table->foreign('speciality_id')
                  ->references('speciality_id')
                  ->on('specialities')
                  ->onDelete('restrict');

            // Indexes
            $table->index('speciality_id');
            $table->index('is_active');
            $table->index('service_name');

            // ⭐ NEW: Indexes for patient booking
            $table->index('is_public');
            $table->index('category');
            $table->index(['is_public', 'is_active']); // Combo index for patient queries
            $table->index(['category', 'display_order']); // For ordered category lists
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
