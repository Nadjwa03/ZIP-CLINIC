<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_details', function (Blueprint $table) {
            $table->id('visit_details_id'); // ✅ PK sesuai ERD

            // Foreign Keys
            $table->unsignedBigInteger('visit_id');
            $table->unsignedBigInteger('service_id')->nullable();

            // FK Constraints
            $table->foreign('visit_id')->references('visit_id')->on('visits')->onDelete('cascade');
            $table->foreign('service_id')->references('service_id')->on('services')->onDelete('set null');
            $table->string('tooth_codes', 50)->nullable();
            $table->string('diagnosis_note', 200)->nullable();
            $table->text('treatment_note')->nullable();
            $table->text('remarks')->nullable();

            // Who performed the treatment (doctor)
            $table->unsignedBigInteger('entered_by')->default(1)->comment('user_id yang input (DOCTOR)');
            $table->foreign('entered_by')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();

            // Indexes
            $table->index('visit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_details');
    }
};