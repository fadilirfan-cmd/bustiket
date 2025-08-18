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
        Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number')->unique();
                $table->unsignedBigInteger('schedule_id'); // relasi ke tabel schedules
                $table->string('passenger_name');
                $table->string('passenger_phone');
                $table->string('jemput');
                $table->string('payment_method'); // contoh: transfer_bca, transfer_bni, dsb
                $table->string('seat_numbers'); // kursi yang dipesan, contoh: 2A,2B
                $table->decimal('total_price', 12, 2);
                $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
