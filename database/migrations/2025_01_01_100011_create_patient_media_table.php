<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_media', function (Blueprint $table) {
            $table->id('media_id'); // ✅ PK = media_id

            // Foreign Keys
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('visit_id')->nullable();

            // FK Constraints
            $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('cascade');
            $table->foreign('visit_id')->references('visit_id')->on('visits')->onDelete('set null');
            $table->enum('media_type', [
                'PHOTO_INTRAORAL', 'PHOTO_EXTRAORAL', 'PHOTO_BEFORE', 'PHOTO_AFTER', 'PHOTO_PROGRESS',
                'XRAY_PERIAPICAL', 'XRAY_PANORAMIC', 'XRAY_CEPHALOMETRIC', 'XRAY_BITEWING',
                'DOC_CONSENT_FORM', 'DOC_MEDICAL_REPORT', 'DOC_LAB_RESULT', 'DOC_OTHER'
            ]);
            $table->string('path', 255);
            $table->string('original_name', 255)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->string('description', 200)->nullable();
            $table->string('tooth_code', 20)->nullable();

            // Who uploaded (user_id from users table - OK to use id)
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');

            $table->timestamp('taken_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('deleted_at')->nullable();

            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index(['patient_id', 'media_type']);
            $table->index(['patient_id', 'visit_id']);
            $table->index('visit_id');
            $table->index('uploaded_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_media');
    }
};