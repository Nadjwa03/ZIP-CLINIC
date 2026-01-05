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
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
