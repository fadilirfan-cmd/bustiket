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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bus_id');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->timestamp('recorded_at');
            $table->timestamps();

            // Foreign key ke tabel buses dengan bus_id sebagai reference
            $table->foreign('bus_id')
                  ->references('bus_id')
                  ->on('buses')
                  ->onDelete('cascade');

            // Index untuk performa
            $table->index('bus_id');
            $table->index('recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
