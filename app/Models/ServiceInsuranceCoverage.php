<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceInsuranceCoverage extends Model
{
    protected $table = 'service_insurance_coverage';

    protected $fillable = [
        'service_id',
        'insurance_id',
        'coverage_percentage',
        'fixed_amount',
        'requires_authorization',
        'is_active'
    ];

    protected $casts = [
        'coverage_percentage' => 'decimal:2',
        'fixed_amount' => 'decimal:2',
        'requires_authorization' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function insurance(): BelongsTo
    {
        return $this->belongsTo(Insurance::class, 'insurance_id');
    }

    // Calcular monto cubierto por el seguro
    public function calculateCoverage(float $price): array
    {
        if ($this->fixed_amount !== null && $this->fixed_amount > 0) {
            $insuranceAmount = min($this->fixed_amount, $price);
            $patientAmount = $price - $insuranceAmount;
            $percentage = ($insuranceAmount / $price) * 100;
        } else {
            $percentage = $this->coverage_percentage ?? 0;
            $insuranceAmount = $price * ($percentage / 100);
            $patientAmount = $price - $insuranceAmount;
        }

        return [
            'insurance_amount' => round($insuranceAmount, 2),
            'patient_amount' => round($patientAmount, 2),
            'percentage' => round($percentage, 2),
            'fixed_amount' => $this->fixed_amount
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}