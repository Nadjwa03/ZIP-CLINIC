<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_time_slots', function (Blueprint $table) {
            $table->id();
            $table->date('slot_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->smallInteger('capacity')->default(3);
            $table->boolean('is_closed')->default(false);
            $table->timestamps();

            // Indexes
            $table->unique(['slot_date', 'start_time']);
            $table->index('slot_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_time_slots');
    }
};