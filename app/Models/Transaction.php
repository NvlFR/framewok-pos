<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Transaction extends Model
{
    use SoftDeletes;

    /**
     * Daftarkan event model.
     */
    protected static function booted(): void
    {
        // Hapus file fisik saat transaksi dihapus (soft delete maupun permanent)
        // Jika ingin tetap ada di soft delete, ganti ke forceDeleting
        static::deleting(function (Transaction $transaction) {
            if ($transaction->isForceDeleting() || ! method_exists($transaction, 'isForceDeleting')) {
                Storage::disk('public')->deleteDirectory("orders/{$transaction->id}");
            }
        });
    }

    protected $fillable = [
        'transaction_number',
        'customer_id',
        'user_id',
        'subtotal',
        'discount_percent',
        'discount_amount',
        'total',
        'payment_method',
        'amount_paid',
        'change_amount',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'change_amount' => 'decimal:2',
        ];
    }

    /**
     * Mendapatkan semua opsi status yang tersedia dari konfigurasi.
     */
    public static function getStatusOptions(): array
    {
        return config('axiom.workflow.statuses', [
            'pending' => ['label' => 'Pending', 'color' => 'warning'],
            'process' => ['label' => 'Diproses', 'color' => 'info'],
            'done'    => ['label' => 'Selesai', 'color' => 'success'],
            'closed'  => ['label' => 'Diambil', 'color' => 'secondary'],
        ]);
    }

    /**
     * Label tampilan untuk setiap status pesanan (Legacy support).
     */
    public const STATUS_LABELS = [
        'pending'  => 'Pending',
        'process'  => 'Diproses',
        'done'     => 'Selesai',
        'closed'   => 'Diambil',
    ];

    /**
     * Mendapatkan pelanggan yang terkait dengan transaksi ini.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Mendapatkan kasir yang membuat transaksi ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan semua item dalam transaksi ini.
     */
    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Mendapatkan label tampilan untuk status saat ini.
     */
    public function getStatusLabelAttribute(): string
    {
        $options = self::getStatusOptions();
        return $options[$this->status]['label'] ?? $this->status;
    }

    /**
     * Mendapatkan warna badge untuk status saat ini.
     */
    public function getStatusColorAttribute(): string
    {
        $options = self::getStatusOptions();
        return $options[$this->status]['color'] ?? 'secondary';
    }
}
