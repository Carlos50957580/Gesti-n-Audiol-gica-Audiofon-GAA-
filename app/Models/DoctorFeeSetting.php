<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorFeeSetting extends Model
{
    use HasFactory;

    protected $table = 'doctor_fees_settings';

    protected $fillable = [
        'doctor_id',
        'category_id',
        'service_id',
        'calculation_type',
        'value',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ── Relaciones ───────────────────────────────────────────────────────────

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    // ── Métodos ──────────────────────────────────────────────────────────────

    /**
     * Calcular el honorario basado en el total de la factura
     * La prioridad es: Servicio específico > Categoría > General
     */
    public function calculateFee($invoiceTotal)
    {
        if ($this->calculation_type === 'percentage') {
            return $invoiceTotal * ($this->value / 100);
        }
        
        return min($this->value, $invoiceTotal);
    }

    /**
     * Obtener la descripción de la configuración
     */
    public function getScopeDescriptionAttribute(): string
    {
        if ($this->service_id) {
            return 'Servicio: ' . ($this->service->name ?? 'N/A');
        }
        
        if ($this->category_id) {
            return 'Categoría: ' . ($this->category->name ?? 'N/A');
        }
        
        return 'General (todos los servicios)';
    }

    /**
     * Verificar si esta configuración aplica a un servicio específico
     */
    public function appliesToService(Service $service): bool
    {
        // Si tiene servicio específico, solo aplica a ese servicio
        if ($this->service_id) {
            return $this->service_id === $service->id;
        }
        
        // Si tiene categoría, aplica a todos los servicios de esa categoría
        if ($this->category_id) {
            return $service->category_id === $this->category_id;
        }
        
        // Si no tiene ni servicio ni categoría, aplica a todos
        return true;
    }

    /**
     * Obtener la configuración de honorarios para un médico y servicio específico
     * Prioridad: Servicio específico > Categoría > General
     */
    public static function getForDoctorAndService($doctorId, $serviceId)
    {
        $service = Service::find($serviceId);
        
        if (!$service) {
            return null;
        }
        
        // 1. Buscar configuración específica del servicio
        $setting = self::where('doctor_id', $doctorId)
            ->where('service_id', $serviceId)
            ->where('is_active', true)
            ->first();
        
        if ($setting) {
            return $setting;
        }
        
        // 2. Buscar configuración por categoría
        $setting = self::where('doctor_id', $doctorId)
            ->where('category_id', $service->category_id)
            ->whereNull('service_id')
            ->where('is_active', true)
            ->first();
        
        if ($setting) {
            return $setting;
        }
        
        // 3. Buscar configuración general (sin categoría ni servicio)
        $setting = self::where('doctor_id', $doctorId)
            ->whereNull('category_id')
            ->whereNull('service_id')
            ->where('is_active', true)
            ->first();
        
        return $setting;
    }
}