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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('whatsapp')->unique();
            $table->enum('gender', ['male', 'female']);
            $table->enum('role', ['admin', 'pic_bus'])->default('pic_bus');
            $table->foreignId('bus_id')->nullable()->constrained()->after('role');
            $table->string('otp', 6)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
