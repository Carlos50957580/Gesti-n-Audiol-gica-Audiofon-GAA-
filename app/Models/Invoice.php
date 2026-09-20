<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Invoice extends Model
{
    protected $fillable = [
        'patient_id',
        'user_id',
        'doctor_id',
        'branch_id',
        'insurance_id',
        'subtotal',
        'tax_amount',
        'total_with_tax',
        'insurance_discount',
        'total',
        'status',
        'authorization_number',
        'with_ncf',
        'ncf',
        'ncf_type',
        'ncf_sequence_id',
        'customer_rnc',
        'customer_business_name',
        'tax_details',
        // ── NUEVOS CAMPOS E-CF ──────────────────────
        'encf',
        'track_id',
        'estado_dgii',
        'qr_link',
        'pdf_cloud_url',
        'ecf_sequence_id',
        'enviada_dgii',
        'enviada_dgii_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_with_tax' => 'decimal:2',
        'insurance_discount' => 'decimal:2',
        'total' => 'decimal:2',
        'with_ncf' => 'boolean',
        'tax_details' => 'array',
        // ── NUEVOS CASTS ────────────────────────────
        'enviada_dgii' => 'boolean',
        'enviada_dgii_at' => 'datetime',
    ];

    // ✅ Accessor para número de factura (usado en recibos)
    public function getInvoiceNumberAttribute(): string
    {
        return 'FAC-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    // ── Relaciones ────────────────────────────────────────────────────────────
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function insurance(): BelongsTo
    {
        return $this->belongsTo(Insurance::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    public function clinicalRecord()
    {
        return $this->hasOne(ClinicalRecord::class);
    }

    public function ncfSequence()
    {
        return $this->belongsTo(NcfSequence::class, 'ncf_sequence_id');
    }

    // app/Models/Invoice.php
public function receipt()
{
    return $this->hasOne(Receipt::class)->latestOfMany();
}

    // ✅ NUEVA: relación con la secuencia e-CF usada
    public function ecfSequence(): BelongsTo
    {
        return $this->belongsTo(EcfSequence::class, 'ecf_sequence_id');
    }

    // ✅ NUEVA: documentos electrónicos (morphMany)
    public function ecfDocuments(): MorphMany
    {
        return $this->morphMany(EcfDocument::class, 'documentable');
    }

    // ✅ NUEVA: honorarios generados por esta factura
    public function doctorFees(): HasMany
    {
        return $this->hasMany(DoctorFee::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────
    public function scopePending($query)
    {
        return $query->where('status', 'pendiente');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'pagada');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelada');
    }

    // ── Métodos de utilidad ───────────────────────────────────────────────────

    public function requiresClinicalRecord()
    {
        foreach ($this->items as $item) {
            $service = $item->service;
            if ($service) {
                if ($service->requires_clinical_record) return true;
                if ($service->category && $service->category->requires_clinical_record) return true;
            }
        }
        return false;
    }

    public function hasClinicalRecord()
    {
        return $this->clinicalRecord()->exists();
    }

    public function getFormattedTotalAttribute()
    {
        $currency = \App\Models\Setting::get('company_currency', 'DOP');
        return $currency . ' ' . number_format($this->total, 2, ',', '.');
    }

    /**
     * ✅ NUEVO: ¿Puede enviarse a la DGII?
     */
    public function puedeEnviarseADgii(): bool
    {
        return $this->status !== 'cancelada'
            && $this->ncf_type !== null
            && !($this->enviada_dgii && $this->estado_dgii === 'aceptado');
    }
}