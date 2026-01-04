<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->uuid('unique_id')->index();

            // simpan hash otp
            $table->string('otp');

            // ✅ tambah 'login'
            $table->enum('type', ['register', 'reset_password', 'login'])->index();

            $table->enum('send_via', ['email', 'sms', 'wa'])->default('email');

            $table->unsignedInteger('resend')->default(0);
            $table->unsignedInteger('attempts')->default(0);

            $table->enum('status', ['active', 'used', 'invalid'])
                ->default('active')
                ->index();

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifications');
    }
};
