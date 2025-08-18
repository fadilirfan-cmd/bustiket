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
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('origin');
            $table->string('destination');
            $table->integer('distance'); // dalam kilometer
            $table->string('duration'); // estimasi waktu perjalanan
            $table->text('description')->nullable();
            $table->json('waypoints')->nullable(); // titik-titik persinggahan
            $table->decimal('base_price', 10, 2)->default(0); // harga dasar perjalanan
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            // Index untuk performa query
            $table->index(['origin', 'destination']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
