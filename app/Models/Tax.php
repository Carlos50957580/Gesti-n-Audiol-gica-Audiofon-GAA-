<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tax extends Model
{
    protected $fillable = [
        'name',
        'code',
        'rate',
        'description',
        'is_active',
        'is_default'
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_active' => 'integer',
        'is_default' => 'integer'
    ];

    // Relación con servicios (many-to-many)
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_tax', 'tax_id', 'service_id')
                    ->withPivot('is_required')
                    ->withTimestamps();
    }

    // Scope para impuestos activos
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    // Scope para impuesto por defecto
    public function scopeDefault($query)
    {
        return $query->where('is_default', 1);
    }

    // Obtener el impuesto por defecto
    public static function getDefault()
    {
        return self::where('is_default', 1)->where('is_active', 1)->first();
    }
}