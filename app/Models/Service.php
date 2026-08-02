<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'category_id',
        'code',
        'name',
        'description',
        'price',
        'duration_minutes',
        'requires_authorization',
        'requires_clinical_record',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_minutes' => 'integer',
        'requires_authorization' => 'integer',
        'requires_clinical_record' => 'integer',
        'is_active' => 'integer'
    ];

    // Relación con categoría
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    // Relación con cobertura de seguros
    public function insuranceCoverage(): HasMany
    {
        return $this->hasMany(ServiceInsuranceCoverage::class, 'service_id');
    }

    // Relación con impuestos (many-to-many)
    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(Tax::class, 'service_tax', 'service_id', 'tax_id')
                    ->withPivot('is_required')
                    ->withTimestamps();
    }

    // Relación con items de factura
    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'service_id');
    }

    // Relación con citas
    public function appointments()
    {
        return $this->belongsToMany(Appointment::class, 'appointment_service', 'service_id', 'appointment_id');
    }

    // Verificar si requiere historia clínica
    public function requiresClinicalRecord(): bool
    {
        if ($this->requires_clinical_record) {
            return true;
        }
        return $this->category?->requires_clinical_record ?? false;
    }

    /**
     * Obtener cobertura para un seguro específico (prioriza la configuración específica)
     */
    public function getCoverageForInsurance(Insurance $insurance): ?ServiceInsuranceCoverage
    {
        // Buscar cobertura específica para este servicio y seguro
        $specificCoverage = $this->insuranceCoverage()
            ->where('insurance_id', $insurance->id)
            ->where('is_active', 1)
            ->first();

        // Si existe cobertura específica, usarla
        if ($specificCoverage) {
            return $specificCoverage;
        }

        // Si no existe, retornar null (se usará la cobertura global del seguro)
        return null;
    }

    /**
     * Calcular cobertura para un servicio con un seguro específico
     */
    public function calculateCoverageWithInsurance(float $subtotal, Insurance $insurance, float $covValue = 0, string $covType = 'pct'): array
    {
        // Buscar cobertura específica
        $specificCoverage = $this->getCoverageForInsurance($insurance);
        
        if ($specificCoverage) {
            // ✅ Usar cobertura específica del servicio
            $calculation = $specificCoverage->calculateCoverage($subtotal);
            return [
                'coverage_percentage' => $calculation['percentage'],
                'insurance_amount' => $calculation['insurance_amount'],
                'patient_amount' => $calculation['patient_amount'],
                'is_specific' => true,
                'coverage_source' => 'Servicio específico'
            ];
        }

        // ✅ Si no hay cobertura específica, usar la cobertura global del seguro
        $globalCoverage = $insurance->coverage_percentage;
        $insuranceAmount = $subtotal * ($globalCoverage / 100);
        $patientAmount = $subtotal - $insuranceAmount;

        return [
            'coverage_percentage' => $globalCoverage,
            'insurance_amount' => $insuranceAmount,
            'patient_amount' => $patientAmount,
            'is_specific' => false,
            'coverage_source' => 'Seguro global'
        ];
    }

    /**
     * Calcular impuestos para el servicio
     */
    public function calculateTaxes(float $subtotal): array
    {
        $taxes = $this->taxes()->where('is_active', 1)->get();
        $totalTax = 0;
        $taxDetails = [];

        foreach ($taxes as $tax) {
            $amount = $subtotal * ($tax->rate / 100);
            $totalTax += $amount;
            $taxDetails[] = [
                'tax_id' => $tax->id,
                'name' => $tax->name,
                'code' => $tax->code,
                'rate' => $tax->rate,
                'amount' => round($amount, 2),
                'is_required' => $tax->pivot->is_required ?? true
            ];
        }

        return [
            'total_tax' => round($totalTax, 2),
            'taxes' => $taxDetails
        ];
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeRequiresClinicalRecord($query)
    {
        return $query->where(function($q) {
            $q->where('requires_clinical_record', 1)
              ->orWhereHas('category', function($q2) {
                  $q2->where('requires_clinical_record', 1);
              });
        });
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}