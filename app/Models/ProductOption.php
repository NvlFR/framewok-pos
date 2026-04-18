<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductOption extends Model
{
    protected $fillable = ['name', 'width', 'height', 'code'];

    /**
     * Mendapatkan semua pricing yang menggunakan opsi produk ini.
     */
    public function priceMatrices(): HasMany
    {
        return $this->hasMany(PriceMatrix::class);
    }
}
