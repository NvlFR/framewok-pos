<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'base_price',
        'unit',
        'has_matrix_pricing',
        'is_active',
        'description',
        'is_pinned',
        'is_custom_size',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'has_matrix_pricing' => 'boolean',
        'is_active' => 'boolean',
        'is_pinned' => 'boolean',
        'is_custom_size' => 'boolean',
    ];

    /**
     * Relasi ke tabel matriks harga.
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ServicePrice::class);
    }
}
