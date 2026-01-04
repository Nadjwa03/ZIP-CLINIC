<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id('visit_id'); // ✅ PK = visit_id

            // Foreign Keys (nullable untuk walk-in tanpa appointment)
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->unsignedBigInteger('queue_id')->nullable();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('doctor_user_id');

            // FK Constraints
            $table->foreign('appointment_id')->references('appointment_id')->on('appointments')->onDelete('set null');
            $table->foreign('queue_id')->references('queue_id')->on('queues')->onDelete('set null');
            $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('cascade');
            $table->foreign('doctor_user_id')->references('doctor_user_id')->on('doctors')->onDelete('cascade');
            $table->timestamp('visit_at');
            $table->enum('status', ['IN_TREATMENT', 'DONE', 'FOLLOW_UP', 'READY_TO_BILL'])->default('IN_TREATMENT');
            
            // SOAP Format
            $table->text('subjective')->nullable();  // S - Keluhan pasien
            $table->text('objective')->nullable();   // O - Pemeriksaan fisik
            $table->text('assessment')->nullable();  // A - Diagnosis
            $table->text('plan')->nullable();        // P - Rencana treatment
            
            $table->timestamp('follow_up_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['patient_id', 'visit_at']);
            $table->index(['doctor_user_id', 'visit_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};