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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bus_id');
            $table->unsignedBigInteger('route_id');
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time');
            $table->integer('price');
            $table->enum('status', ['active', 'cancelled', 'completed'])->default('active');
            $table->timestamps();

            // Foreign keys
            $table->foreign('bus_id')->references('bus_id')->on('buses')->onDelete('cascade');
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');

            // Indexes
            $table->index('departure_time');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
