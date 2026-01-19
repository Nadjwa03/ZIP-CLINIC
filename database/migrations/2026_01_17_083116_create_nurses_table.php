<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table: nurses
     * 
     * Menyimpan data perawat/asisten dokter.
     * Mirip dengan doctors table, nurse_user_id adalah FK ke users.
     */
    public function up(): void
    {
        Schema::create('nurses', function (Blueprint $table) {
            // Primary Key = nurse_user_id (sama seperti doctors)
            $table->unsignedBigInteger('nurse_user_id')->primary();
            $table->foreign('nurse_user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Profile Info
            $table->string('name', 120)->comment('Nama lengkap perawat');
            $table->string('phone', 30)->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nurses');
    }
};