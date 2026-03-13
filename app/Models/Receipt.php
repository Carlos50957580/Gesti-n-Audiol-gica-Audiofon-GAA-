<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'user_id',
        'branch_id',
        'cash_amount',
        'card_amount',
        'transfer_amount',
        'total_paid',
        'card_reference',
        'transfer_reference',
        'notes',
    ];

    protected $casts = [
        'cash_amount'     => 'decimal:2',
        'card_amount'     => 'decimal:2',
        'transfer_amount' => 'decimal:2',
        'total_paid'      => 'decimal:2',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /** Número de recibo legible: REC-000001 */
    public function getReceiptNumberAttribute(): string
    {
        return 'REC-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /** Etiqueta de métodos usados: "Efectivo + Tarjeta" */
    public function getPaymentSummaryAttribute(): string
    {
        $parts = [];
        if ($this->cash_amount     > 0) $parts[] = 'Efectivo';
        if ($this->card_amount     > 0) $parts[] = 'Tarjeta';
        if ($this->transfer_amount > 0) $parts[] = 'Transferencia';
        return implode(' + ', $parts) ?: '—';
    }

    /** ¿Fue pago mixto? */
    public function getIsMixedAttribute(): bool
    {
        $used = collect([
            $this->cash_amount,
            $this->card_amount,
            $this->transfer_amount,
        ])->filter(fn($v) => $v > 0)->count();

        return $used > 1;
    }
}
