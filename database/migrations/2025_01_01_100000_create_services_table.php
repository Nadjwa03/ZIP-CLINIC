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
            $table->string('code', 40)->unique();
            $table->string('name', 160);
            $table->string('category', 50)->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->unsignedSmallInteger('duration_minutes')->default(30);
            $table->string('icon', 10)->nullable();
            $table->string('image_path')->nullable();
            $table->integer('display_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes - HANYA SATU KALI
            $table->index('display_order');
            $table->index('is_active');
            $table->index('category'); // Hanya 1x, jangan duplikat!
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
