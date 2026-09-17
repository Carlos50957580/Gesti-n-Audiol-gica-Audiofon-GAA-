<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'patient_id', 'user_id', 'branch_id',
        'subtotal', 'tax_amount', 'total_with_tax', 'discount', 'total',
        'paid_amount', 'balance',
        'status', 'with_ncf', 'ncf', 'ncf_type', 'ncf_sequence_id',
        'customer_rnc', 'customer_business_name', 'notes',
    'encf', 'track_id', 'estado_dgii', 'qr_link', 'pdf_cloud_url',
    'ecf_sequence_id', 'enviada_dgii', 'enviada_dgii_at',
    ];

    protected $casts = [
        'subtotal'       => 'decimal:2',
        'tax_amount'     => 'decimal:2',
        'total_with_tax' => 'decimal:2',
        'discount'       => 'decimal:2',
        'total'          => 'decimal:2',
        'paid_amount'    => 'decimal:2',
        'balance'        => 'decimal:2',
        'with_ncf'       => 'boolean',
        'enviada_dgii' => 'boolean',
    'enviada_dgii_at' => 'datetime',
        
    ];

    // ── Relaciones ──────────────────────────────────────────
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(ProductInvoiceItem::class);
    }

    public function receipts()
    {
        return $this->hasMany(ProductReceipt::class);
    }

    // ── Accessors ───────────────────────────────────────────
    public function getFormattedTotalAttribute()
    {
        return 'RD$ ' . number_format($this->total, 2);
    }

    public function getFormattedPaidAttribute()
    {
        return 'RD$ ' . number_format($this->paid_amount, 2);
    }

    public function getFormattedBalanceAttribute()
    {
        return 'RD$ ' . number_format($this->balance, 2);
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pendiente'      => 'Pendiente',
            'pagada_parcial' => 'Pago Parcial',
            'pagada'         => 'Pagada',
            'cancelada'      => 'Cancelada',
            default          => $this->status,
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pendiente'      => 'warning',
            'pagada_parcial' => 'info',
            'pagada'         => 'success',
            'cancelada'      => 'danger',
            default          => 'secondary',
        };
    }

    // ── Helpers ─────────────────────────────────────────────
    public static function generateNumber(): string
    {
        $prefix = 'FAC-PROD-';
        $last = self::where('number', 'like', $prefix . '%')
                    ->orderByDesc('id')->first();
        $next = $last ? ((int) substr($last->number, -6)) + 1 : 1;
        return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    public function isPaid()
    {
        return $this->status === 'pagada';
    }

    public function isPartiallyPaid()
    {
        return $this->status === 'pagada_parcial';
    }

    public function isPending()
    {
        return $this->status === 'pendiente';
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pendiente', 'pagada_parcial']);
    }

    /**
 * Último recibo (pago más reciente)
 */
public function receipt()
{
    return $this->hasOne(ProductReceipt::class)->latestOfMany();
}

    /**
     * Recalcular el balance basado en pagos recibidos
     */
    public function recalculateBalance()
    {
        $this->paid_amount = $this->receipts()->sum('total_paid');
        $this->balance = max(0, $this->total - $this->paid_amount);

        // Actualizar estado automáticamente
        if ($this->balance <= 0.01 && $this->paid_amount > 0) {
            $this->status = 'pagada';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'pagada_parcial';
        } else {
            $this->status = 'pendiente';
        }

        $this->save();
        return $this;
    }

    /**
 * Secuencia NCF usada en esta factura
 */
public function ncfSequence()
{
    return $this->belongsTo(NcfSequence::class, 'ncf_sequence_id');
}

// ── Nuevas relaciones ──
public function ecfSequence()
{
    return $this->belongsTo(EcfSequence::class, 'ecf_sequence_id');
}

public function ecfDocuments()
{
    return $this->morphMany(EcfDocument::class, 'documentable');
}

// ── Nuevos accessors ──
public function getEstadoDgiiLabelAttribute()
{
    return match($this->estado_dgii) {
        'aceptado'     => 'Aceptado por DGII',
        'rechazado'    => 'Rechazado',
        'en_proceso'   => 'En proceso',
        'condicional'  => 'Aceptado condicional',
        default        => $this->enviada_dgii ? 'Enviado' : 'No enviado',
    };
}

public function getEstadoDgiiColorAttribute()
{
    return match($this->estado_dgii) {
        'aceptado'     => 'success',
        'rechazado'    => 'danger',
        'en_proceso'   => 'warning',
        'condicional'  => 'info',
        default        => 'secondary',
    };
}

public function puedeEnviarDgii(): bool
{
    return $this->status !== 'cancelada'
        && !$this->enviada_dgii;
}

}