<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'service_id',
        'price',
        'quantity',
        'subtotal',
        'coverage_percentage',
        'insurance_amount',
        'patient_amount',
        'tax_amount',
        'tax_details',
        'total_with_tax'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'coverage_percentage' => 'decimal:2',
        'insurance_amount' => 'decimal:2',
        'patient_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_with_tax' => 'decimal:2',
        'tax_details' => 'array'
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}