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
        Schema::create('doctors', function (Blueprint $table) {
            // Primary Key = doctor_user_id (sesuai ERD)
            $table->unsignedBigInteger('doctor_user_id')->primary();
            $table->foreign('doctor_user_id')->references('id')->on('users')->onDelete('cascade');

            // Professional Info
            $table->string('registration_number', 50)->unique()->comment('SIP/STR number');
            $table->string('display_name', 120)->comment('Display name for public');
            $table->unsignedBigInteger('speciality_id');

            // Contact
            $table->string('phone', 30)->nullable();

            // Profile
            $table->text('bio')->nullable()->comment('Doctor biography');
            $table->string('photo_path', 255)->nullable()->comment('Profile photo');

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('speciality_id')
                  ->references('speciality_id')
                  ->on('specialities')
                  ->onDelete('restrict');

            // Indexes
            $table->index('is_active');
            $table->index('speciality_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
