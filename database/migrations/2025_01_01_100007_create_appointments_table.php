<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Appointments untuk scheduling + queue number (urutan booking)
     * Medical records ada di tabel visits (SOAP format)
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id('appointment_id');
            
            // Foreign Keys
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('doctor_user_id');
            
            // Schedule
            $table->dateTime('scheduled_start_at');
            $table->dateTime('scheduled_end_at');
            $table->text('notes')->nullable();    
            
            // ✅ QUEUE FIELDS - PERLU untuk sistem queue berdasarkan booking!
            $table->integer('queue_number')->nullable();
            $table->date('queue_date')->nullable();
            
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
            $table->timestamp('cancelled_at')->nullable();
            
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
            
            // Indexes
            $table->index(['doctor_user_id', 'scheduled_start_at']);
            $table->index(['doctor_user_id', 'queue_date', 'queue_number']);
            $table->index('patient_id');
            $table->index('status');
            
            // Unique constraint: Dokter tidak bisa double booking
            $table->unique(['doctor_user_id', 'scheduled_start_at', 'scheduled_end_at'], 'uq_doctor_timeslot');
        });
        
        // Add check constraint
        DB::statement('ALTER TABLE appointments ADD CONSTRAINT chk_appointment_time CHECK (scheduled_end_at > scheduled_start_at)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};