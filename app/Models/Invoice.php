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
    // Este es el que se usa en las vistas
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

    // ============================================
    // MÉTODOS DE UTILIDAD
    // ============================================
    
    /**
     * Verifica si la factura requiere historia clínica
     */
    public function requiresClinicalRecord()
    {
        // Verificar si algún servicio de la factura requiere historia clínica
        foreach ($this->items as $item) {
            $service = $item->service;
            if ($service) {
                // Verificar directamente en el servicio
                if ($service->requires_clinical_record) {
                    return true;
                }
                // Verificar en la categoría del servicio
                if ($service->category && $service->category->requires_clinical_record) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Verifica si la factura ya tiene una historia clínica asociada
     */
    public function hasClinicalRecord()
    {
        return $this->clinicalRecord()->exists();
    }

    /**
     * Obtiene el total formateado con moneda
     */
    public function getFormattedTotalAttribute()
    {
        $currency = \App\Models\Setting::get('company_currency', 'DOP');
        return $currency . ' ' . number_format($this->total, 2, ',', '.');
    }
}