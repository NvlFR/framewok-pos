<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel transaction_items sebagai detail item per transaksi.
     * Harga di-snapshot agar tidak terpengaruh perubahan harga layanan di masa depan.
     */
    public function up(): void
    {
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('service_name'); // snapshot nama layanan saat transaksi
            $table->foreignId('variant_id')->nullable()->constrained('variants')->nullOnDelete();
            $table->string('variant_name')->nullable(); // snapshot nama varian
            $table->enum('modifier', ['premium', 'standar', 'na'])->default('na'); // jenis atribut
            $table->integer('qty');
            $table->decimal('unit_price', 12, 2); // snapshot harga saat transaksi
            $table->decimal('subtotal', 12, 2);
            $table->string('file_path')->nullable(); // path file upload custom order
            $table->string('original_filename')->nullable(); // nama file asli
            $table->text('item_notes')->nullable(); // catatan khusus per item
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
