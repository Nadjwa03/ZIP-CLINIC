<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id('patient_id'); // ✅ FIXED: Sesuai ERD
            
            // Owner User (nullable for unclaimed patients)
            $table->unsignedBigInteger('owner_user_id')->nullable();
            $table->foreign('owner_user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Medical Record
            $table->string('medical_record_number', 30)->unique();
            
            // Identity Information ✅ NEW!
            $table->string('id_type', 20)->nullable();     // KTP, SIM, PASSPORT, KK
            $table->string('id_number', 50)->nullable();   // Identity number
            
            // Basic Info
            $table->string('full_name', 120);
            $table->string('email')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            
            // Emergency Contact
            $table->string('emergency_contact_name', 120)->nullable();
            $table->string('emergency_contact_phone', 30)->nullable();
            $table->string('emergency_contact_relation', 50)->nullable();
            
            // Medical Info
            $table->string('blood_type', 5)->nullable();
            $table->text('primary_complaint')->nullable();
            $table->text('medical_history')->nullable();
            $table->text('allergies')->nullable();
            
            // Claim System
            $table->string('secret_code', 50)->nullable();
            $table->timestamp('claimed_at')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            //last visit
            $table->timestamp('last_visit_at')->nullable();
            $table->string('last_treatment')->nullable();
            
            // Indexes
            $table->index('medical_record_number');
            $table->index('owner_user_id');
            $table->index('id_number');  // ✅ NEW INDEX!
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
