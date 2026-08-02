<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'customer_rnc',
        'customer_business_name',
        'tax_details'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_with_tax' => 'decimal:2',
        'insurance_discount' => 'decimal:2',
        'total' => 'decimal:2',
        'with_ncf' => 'boolean',
        'tax_details' => 'array'
    ];

    // ✅ Accessor para número de factura (usado en recibos)
    public function getInvoiceNumberAttribute(): string
    {
        return 'FAC-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    // Relaciones
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

    // ✅ Scopes para estados
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
}