<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'color',
        'requires_clinical_record',
        'is_active'
    ];

    // ✅ CAMBIAR DE 'boolean' a 'integer'
    protected $casts = [
        'requires_clinical_record' => 'integer',
        'is_active' => 'integer'
    ];

    // Relación con servicios (estudios)
    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'category_id');
    }

    // Servicios activos
    public function activeServices(): HasMany
    {
        return $this->services()->where('is_active', 1);
    }

    // Scope para categorías activas
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    // Scope para categorías que requieren historia clínica
    public function scopeRequiresClinicalRecord($query)
    {
        return $query->where('requires_clinical_record', 1);
    }

    // app/Models/ServiceCategory.php - Agregar relación

// Relación con configuraciones de honorarios
public function doctorFeeSettings()
{
    return $this->hasMany(DoctorFeeSetting::class, 'category_id');
}
}