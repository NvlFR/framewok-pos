<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel service_prices sebagai pricing matrix:
     * harga per layanan berdasarkan varian dan atribut.
     */
    public function up(): void
    {
        Schema::create('service_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('variants')->nullOnDelete();
            $table->enum('modifier', ['premium', 'standar', 'na'])->default('na'); // premium, standar, tidak berlaku
            $table->decimal('price', 12, 2);
            $table->timestamps();

            // Kombinasi service + variant + modifier harus unik
            $table->unique(['service_id', 'variant_id', 'modifier'], 'unique_service_price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_prices');
    }
};
