<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'invoice_date',
        'insurance_id', 'branch_id', 'user_id', 'invoice_id',
        'subtotal', 'tax_amount', 'total', 'with_tax',
        'concept', 'status', 'paid_amount', 'balance',
        'with_ncf', 'ncf', 'ncf_type', 'ncf_sequence_id',
        'customer_rnc', 'customer_business_name',
        'encf', 'track_id', 'estado_dgii', 'qr_link', 'pdf_cloud_url',
        'ecf_sequence_id', 'enviada_dgii', 'enviada_dgii_at',
        'notes',
    ];

    protected $casts = [
        'invoice_date'      => 'date',
        'subtotal'          => 'decimal:2',
        'tax_amount'        => 'decimal:2',
        'total'             => 'decimal:2',
        'paid_amount'       => 'decimal:2',
        'balance'           => 'decimal:2',
        'with_tax'          => 'boolean',
        'with_ncf'          => 'boolean',
        'enviada_dgii'      => 'boolean',
        'enviada_dgii_at'   => 'datetime',
    ];

    // ── Relaciones ──────────────────────────────────────
    public function insurance()
    {
        return $this->belongsTo(Insurance::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function ncfSequence()
    {
        return $this->belongsTo(NcfSequence::class, 'ncf_sequence_id');
    }

    public function ecfSequence()
    {
        return $this->belongsTo(EcfSequence::class, 'ecf_sequence_id');
    }

    public function ecfDocuments()
    {
        return $this->morphMany(EcfDocument::class, 'documentable');
    }

    // ── Accessors ───────────────────────────────────────
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

    public function getEstadoDgiiLabelAttribute()
    {
        return match($this->estado_dgii) {
            'aceptado'    => 'Aceptado por DGII',
            'rechazado'   => 'Rechazado',
            'en_proceso'  => 'En proceso',
            'condicional' => 'Aceptado condicional',
            default       => $this->enviada_dgii ? 'Enviado' : 'No enviado',
        };
    }

    public function getEstadoDgiiColorAttribute()
    {
        return match($this->estado_dgii) {
            'aceptado'    => 'success',
            'rechazado'   => 'danger',
            'en_proceso'  => 'warning',
            'condicional' => 'info',
            default       => 'secondary',
        };
    }

    // ── Helpers ─────────────────────────────────────────
    public static function generateNumber(): string
    {
        $prefix = 'FAC-ARS-';
        $last = self::where('number', 'like', $prefix . '%')
                    ->orderByDesc('id')->first();
        $next = $last ? ((int) substr($last->number, -6)) + 1 : 1;
        return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pendiente', 'pagada_parcial']);
    }

    public function puedeEnviarseADgii(): bool
    {
        return $this->status !== 'cancelada'
            && !$this->enviada_dgii;
    }

    public function recalculateBalance()
    {
        $this->balance = max(0, $this->total - $this->paid_amount);

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
}