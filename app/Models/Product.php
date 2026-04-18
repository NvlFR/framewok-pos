<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'base_price',
        'unit',
        'has_matrix_pricing',
        'is_active',
        'is_metered', // Pengganti is_per_meter agar bisa buat barang lain (kabel, kain, dll)
        'is_pinned',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'has_matrix_pricing' => 'boolean',
            'is_active' => 'boolean',
            'is_metered' => 'boolean',
            'is_pinned' => 'boolean',
        ];
    }

    /**
     * Mendapatkan semua pricing matrix (variasi harga) untuk produk ini.
     */
    public function priceMatrices(): HasMany
    {
        return $this->hasMany(PriceMatrix::class);
    }

    /**
     * Mendapatkan semua item transaksi yang menggunakan produk ini.
     */
    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Mendapatkan harga berdasarkan opsi varian dan tipe spesifikasi.
     */
    public function getPriceFor(?int $optionId, string $type = 'na'): float
    {
        if ($this->has_matrix_pricing) {
            $price = $this->priceMatrices()
                ->where('product_option_id', $optionId)
                ->where('type', $type)
                ->first();

            return $price ? (float) $price->price : (float) $this->base_price;
        }

        return (float) $this->base_price;
    }
}
