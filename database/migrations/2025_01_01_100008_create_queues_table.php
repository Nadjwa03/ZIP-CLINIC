<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queues', function (Blueprint $table) {

            // PRIMARY KEY
            $table->id('queue_id');
            
            // ==========================================
            // FOREIGN KEYS - Sesuai ERD
            // ==========================================
            
            // Link to appointment (NULL untuk walk-in)
            $table->unsignedBigInteger('appointment_id')->nullable();
            
            // Patient (wajib)
            $table->unsignedBigInteger('patient_id');
            
            // Doctor yang handle antrian
            $table->unsignedBigInteger('doctor_user_id');
            
           
            // QUEUE INFO
            $table->date('queue_date');
            $table->unsignedSmallInteger('queue_number');
            $table->time('estimated_time')->nullable();
            

            $table->enum('status', [
                'WAITING',      
                'CALLED',       
                'IN_TREATMENT',
                'DONE',         
                'CANCELLED',    
                'SKIPPED'       
            ])->default('WAITING');
            
            $table->text('complaint')->nullable();
            $table->text('cancel_reason')->nullable();

            // PRIORITY - Untuk VIP/Urgent patients
            $table->enum('priority', ['NORMAL', 'VIP', 'URGENT'])->default('NORMAL'); 
            
            // ==========================================
            // TIMESTAMPS - IMPROVEMENT untuk tracking detail
            // ==========================================
            $table->timestamp('called_at')->nullable()->comment('Waktu dipanggil');
            $table->unsignedBigInteger('called_by')->nullable()->comment('User yang memanggil (admin/nurse)');
            
            // ✨ SARAN IMPROVEMENT: Tambah tracking waktu lebih detail
            $table->timestamp('checked_in_at')->nullable()->comment('Waktu pasien check-in');
            $table->timestamp('started_at')->nullable()->comment('Waktu mulai treatment');
            $table->timestamp('completed_at')->nullable()->comment('Waktu selesai treatment');
            
            $table->timestamps(); // created_at, updated_at
            
            // ==========================================
            // FOREIGN KEY CONSTRAINTS
            // ==========================================
            $table->foreign('appointment_id')
                ->references('appointment_id')
                ->on('appointments')
                ->nullOnDelete();
            
            $table->foreign('patient_id')
                ->references('patient_id')
                ->on('patients')
                ->cascadeOnDelete();
            
            $table->foreign('doctor_user_id')
                ->references('doctor_user_id')
                ->on('doctors')
                ->restrictOnDelete(); // Jangan hapus dokter yang masih ada antrian
            
            // ==========================================
            // INDEXES & CONSTRAINTS
            // ==========================================
            
            // PILIHAN A: Nomor antrian GLOBAL per hari (1 nomor untuk semua dokter)
            // Contoh: Nomor 1, 2, 3, ... untuk seluruh klinik
            $table->unique(['queue_date', 'queue_number'], 'uq_daily_queue_number');
            
            // PILIHAN B: Nomor antrian PER DOKTER per hari (RECOMMENDED untuk multi-dokter)
            // Contoh: Dokter A: 1,2,3... | Dokter B: 1,2,3...
            // Uncomment jika pakai sistem per dokter:
            // $table->unique(['doctor_user_id', 'queue_date', 'queue_number'], 'uq_doctor_daily_queue');
            
            // Performance indexes
            $table->index(['doctor_user_id', 'queue_date', 'status'], 'idx_doctor_queue');
            $table->index('appointment_id');
            $table->index(['patient_id', 'queue_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
