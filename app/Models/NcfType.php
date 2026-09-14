<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NcfType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'description', 'category', 'is_electronic', 'is_active'
    ];

    protected $casts = [
        'is_electronic' => 'boolean',
        'is_active'     => 'boolean',
    ];

    // ── Relaciones ──────────────────────────────────────────
    public function sequences()
    {
        return $this->hasMany(NcfSequence::class);
    }

    // ── Scopes ──────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeIngresos($query)
    {
        return $query->where('category', 'ingresos');
    }

    // ── Accessors ───────────────────────────────────────────
    public function getCategoryLabelAttribute()
    {
        return match($this->category) {
            'ingresos'      => 'Ingresos',
            'gastos'        => 'Gastos',
            'gubernamental' => 'Gubernamental',
            'regimen'       => 'Régimen Especial',
            'especial'      => 'Especial',
            default         => $this->category,
        };
    }
}