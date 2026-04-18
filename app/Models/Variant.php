<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Variant extends Model
{
    protected $fillable = ['name', 'width', 'height'];

    /**
     * Relasi ke harga layanan cetak.
     */
    public function servicePrices(): HasMany
    {
        return $this->hasMany(ServicePrice::class);
    }
}
