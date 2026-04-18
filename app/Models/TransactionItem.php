<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id',
        'service_id',
        'service_name',
        'variant_id',
        'variant_name',
        'modifier',
        'qty',
        'width',
        'height',
        'unit_price',
        'subtotal',
        'file_path',
        'original_filename',
        'item_notes',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'qty' => 'integer',
            'width' => 'decimal:2',
            'height' => 'decimal:2',
        ];
    }

    /**
     * Mendapatkan transaksi induk dari item ini.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Mendapatkan layanan yang digunakan dalam item ini.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Mendapatkan ukuran/varian yang digunakan dalam item ini.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }

    /**
     * Mendapatkan label tampilan untuk modifier/atribut.
     */
    public function getModifierLabelAttribute()
    {
        $all = config('axiom.labels.attribute_values', []);
        return $all[$this->attribute] ?? $all[$this->modifier] ?? '-';
    }
}
