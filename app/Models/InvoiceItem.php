<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'service_id',
        'price',
        'quantity',
        'subtotal',
        'coverage_percentage',
        'insurance_amount',
        'patient_amount',
    ];

    protected $casts = [
        'price'               => 'decimal:2',
        'subtotal'            => 'decimal:2',
        'coverage_percentage' => 'decimal:2',
        'insurance_amount'    => 'decimal:2',
        'patient_amount'      => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}