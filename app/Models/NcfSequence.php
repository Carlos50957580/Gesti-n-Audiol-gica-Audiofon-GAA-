<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NcfSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'ncf_type_id', 'branch_id', 'prefix', 'serie',
        'start_number', 'end_number', 'current_number',
        'alert_threshold', 'valid_from', 'valid_until',
        'is_active', 'notes',
    ];

    protected $casts = [
        'valid_from'      => 'date',
        'valid_until'     => 'date',
        'is_active'       => 'boolean',
        'alert_threshold' => 'integer',
    ];

    // ── Relaciones ──────────────────────────────────────────
    public function type()
    {
        return $this->belongsTo(NcfType::class, 'ncf_type_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // ── Scopes ──────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeExpired($query)
    {
        return $query->where('valid_until', '<', now()->toDateString());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereBetween('valid_until', [
            now()->toDateString(),
            now()->addDays($days)->toDateString()
        ]);
    }

    // ── Accessors ───────────────────────────────────────────
    /**
     * NCF completo actual (prefix + serie + current_number)
     */
    public function getCurrentNcfAttribute()
    {
        return $this->prefix . $this->serie . str_pad($this->current_number, 8, '0', STR_PAD_LEFT);
    }

    /**
     * NCF inicial
     */
    public function getStartNcfAttribute()
    {
        return $this->prefix . $this->serie . str_pad($this->start_number, 8, '0', STR_PAD_LEFT);
    }

    /**
     * NCF final
     */
    public function getEndNcfAttribute()
    {
        return $this->prefix . $this->serie . str_pad($this->end_number, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Total de números disponibles en la secuencia
     */
    public function getTotalAvailableAttribute()
    {
        return (int) $this->end_number - (int) $this->start_number + 1;
    }

    /**
     * Números restantes desde el actual
     */
    public function getRemainingAttribute()
    {
        return (int) $this->end_number - (int) $this->current_number + 1;
    }

    /**
     * Números usados
     */
    public function getUsedAttribute()
    {
        return (int) $this->current_number - (int) $this->start_number;
    }

    /**
     * Porcentaje usado
     */
    public function getUsedPercentAttribute()
    {
        if ($this->total_available <= 0) return 0;
        return round(($this->used / $this->total_available) * 100, 1);
    }

    /**
     * Días restantes de vigencia
     */
    public function getDaysLeftAttribute()
    {
        if (!$this->valid_until) return null;
        return max(0, now()->diffInDays($this->valid_until, false));
    }

    /**
     * Estado de la secuencia (label + color)
     */
    public function getStatusInfoAttribute()
    {
        if (!$this->is_active) {
            return ['label' => 'Inactiva', 'color' => 'secondary'];
        }

        if ($this->days_left !== null && $this->days_left < 0) {
            return ['label' => 'Vencida', 'color' => 'danger'];
        }

        if ($this->remaining <= 0) {
            return ['label' => 'Agotada', 'color' => 'danger'];
        }

        if ($this->remaining <= $this->alert_threshold) {
            return ['label' => 'Por agotarse', 'color' => 'warning'];
        }

        if ($this->days_left !== null && $this->days_left <= 30) {
            return ['label' => 'Próxima a vencer', 'color' => 'warning'];
        }

        return ['label' => 'Activa', 'color' => 'success'];
    }

    public function getStatusLabelAttribute()
    {
        return $this->status_info['label'];
    }

    public function getStatusColorAttribute()
    {
        return $this->status_info['color'];
    }

    // ── Helpers ─────────────────────────────────────────────
    /**
     * Verifica si la secuencia puede emitir el siguiente NCF
     */
    public function canIssue(): bool
    {
        if (!$this->is_active) return false;
        if ($this->days_left !== null && $this->days_left < 0) return false;
        if ($this->remaining <= 0) return false;
        return true;
    }

    /**
     * Obtiene el próximo NCF y avanza el contador
     */
    public function issueNext(): ?string
    {
        if (!$this->canIssue()) return null;

        $ncf = $this->current_ncf;

        // Avanzar el contador
        $this->current_number = str_pad((int) $this->current_number + 1, 8, '0', STR_PAD_LEFT);
        $this->save();

        return $ncf;
    }

    /**
     * Obtener siguiente NCF sin consumirlo (solo para preview)
     */
    public function peekNext(): string
    {
        return $this->current_ncf;
    }
}