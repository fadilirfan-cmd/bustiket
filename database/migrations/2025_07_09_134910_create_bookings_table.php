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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schedule_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('passenger_name');
            $table->string('passenger_phone');
            $table->string('seat_number');
            $table->integer('total_amount');
            $table->string('payment_method')->nullable();
            $table->string('payment_proof')->nullable();
            $table->enum('status', ['pending','confirmed','cancelled','completed'])->default('pending');
            $table->dateTime('booking_date')->useCurrent();
            $table->timestamps();

            // Foreign key
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['schedule_id','user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
