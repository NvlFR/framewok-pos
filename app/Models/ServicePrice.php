<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePrice extends Model
{
    protected $fillable = [
        'service_id',
        'paper_size_id',
        'print_type',
        'price',
    ];

    /**
     * Relasi ke layanan.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relasi ke ukuran kertas.
     */
    public function paperSize(): BelongsTo
    {
        return $this->belongsTo(PaperSize::class);
    }
}
