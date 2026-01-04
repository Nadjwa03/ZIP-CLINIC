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
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id();

            // Foreign Key
            $table->unsignedBigInteger('doctor_user_id');
            $table->foreign('doctor_user_id')->references('doctor_user_id')->on('doctors')->onDelete('cascade');
            
            // Schedule Info
            $table->tinyInteger('day_of_week')->comment('1=Monday, 7=Sunday');
            $table->time('start_time')->comment('e.g., 09:00');
            $table->time('end_time')->comment('e.g., 17:00');
            
            // Effective Period (optional)
            $table->date('effective_from')->nullable()->comment('Schedule starts from');
            $table->date('effective_to')->nullable()->comment('Schedule ends at');
            
            // Status
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['doctor_user_id', 'day_of_week']);
            $table->index(['doctor_user_id', 'is_active']);
            $table->index(['effective_from', 'effective_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_schedules');
    }
};
