<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id('appointment_id');
            
            // Foreign Keys
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('doctor_user_id'); // Assigned doctor
            $table->unsignedBigInteger('slot_id')->nullable(); // Jika pakai time slot
            
            // Schedule
            $table->dateTime('scheduled_start_at');
            $table->dateTime('scheduled_end_at');
            
            // Appointment Info
            $table->text('complaint')->nullable();
            $table->enum('status', [
                'BOOKED', 
                'CHECKED_IN', 
                'IN_TREATMENT', 
                'COMPLETED', 
                'CANCELLED'
            ])->default('BOOKED');
            
            $table->enum('booking_source', ['WEB', 'WALK_IN'])->default('WEB');
            
            // Cancellation
            $table->text('cancel_reason')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Foreign Key Constraints
            $table->foreign('patient_id')
                  ->references('patient_id')
                  ->on('patients')
                  ->onDelete('cascade');
                  
            $table->foreign('service_id')
                  ->references('service_id')
                  ->on('services')
                  ->onDelete('restrict');
                  
            $table->foreign('doctor_user_id')
                  ->references('doctor_user_id')
                  ->on('doctors')
                  ->onDelete('restrict');
                  
            $table->foreign('slot_id')
                  ->references('id')
                  ->on('clinic_time_slots')
                  ->onDelete('set null');
            
            // Indexes
            $table->index(['doctor_user_id', 'scheduled_start_at']);
            $table->index('patient_id');
            $table->index('status');
            
            // Unique constraint: Dokter tidak bisa double booking
            $table->unique(['doctor_user_id', 'scheduled_start_at', 'scheduled_end_at'], 'uq_doctor_timeslot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
