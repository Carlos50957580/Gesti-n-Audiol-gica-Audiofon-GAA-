<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EcfSequence extends Model
{
    use HasFactory;

    protected $table = 'ecf_sequences';

    protected $fillable = [
        'tipo_ecf',
        'prefijo',
        'desde',
        'hasta',
        'secuencia_actual',
        'fecha_vencimiento',
        'valid_from',
        'estado',
        'branch_id',
        'notas',
    ];

    protected $casts = [
        'desde' => 'integer',
        'hasta' => 'integer',
        'secuencia_actual' => 'integer',
        'fecha_vencimiento' => 'date',
        'valid_from' => 'date',
        'estado' => 'boolean',
    ];

    // ==========================================
    // Relaciones
    // ==========================================

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function productInvoices()
    {
        return $this->hasMany(ProductInvoice::class, 'ecf_sequence_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'ecf_sequence_id');
    }

    // ==========================================
    // Accessors
    // ==========================================

    /**
     * Total de secuencias del rango
     */
    public function getTotalSecuenciasAttribute(): int
    {
        return ($this->hasta - $this->desde) + 1;
    }

    /**
     * Secuencias usadas
     */
    public function getSecuenciasUsadasAttribute(): int
    {
        if ($this->secuencia_actual < $this->desde) {
            return 0;
        }
        return $this->secuencia_actual - $this->desde + 1;
    }

    /**
     * Secuencias disponibles
     */
    public function getSecuenciasDisponiblesAttribute(): int
    {
        return max(0, $this->hasta - max($this->secuencia_actual, $this->desde - 1));
    }

    /**
     * Porcentaje de uso
     */
    public function getPorcentajeUsoAttribute(): float
    {
        $total = $this->total_secuencias;
        if ($total <= 0) return 0;
        return round(($this->secuencias_usadas / $total) * 100, 2);
    }

    /**
     * Nombre completo del e-CF (ej: E310000000001)
     */
    public function getSiguienteEncfAttribute(): string
    {
        $siguiente = $this->secuencia_actual < $this->desde
            ? $this->desde
            : $this->secuencia_actual + 1;

        return $this->prefijo . str_pad($siguiente, 10, '0', STR_PAD_LEFT);
    }

    // ==========================================
    // Métodos de negocio
    // ==========================================

    /**
     * Verifica si la secuencia está vencida
     */
    public function estaVencida(): bool
    {
        // El tipo E32 no requiere fecha de vencimiento
        if ($this->tipo_ecf === '32') {
            return false;
        }
        return $this->fecha_vencimiento && $this->fecha_vencimiento->isPast();
    }

    /**
     * Verifica si quedan secuencias disponibles
     */
    public function tieneDisponibles(): bool
    {
        return $this->secuencia_actual < $this->hasta;
    }

    /**
     * Verifica si está en alerta (pocas secuencias)
     */
    public function enAlerta(): bool
    {
        return $this->secuencias_disponibles <= 50;
    }

    /**
     * Obtiene y avanza la secuencia actual
     */
    public function obtenerSiguiente(): string
    {
        $siguiente = $this->secuencia_actual < $this->desde
            ? $this->desde
            : $this->secuencia_actual + 1;

        if ($siguiente > $this->hasta) {
            throw new \Exception("La secuencia {$this->prefijo} se ha agotado.");
        }

        return $this->prefijo . str_pad($siguiente, 10, '0', STR_PAD_LEFT);
    }

    /**
     * Avanza la secuencia actual (después de usar)
     */
    public function avanzar(): void
    {
        $this->secuencia_actual = $this->secuencia_actual < $this->desde
            ? $this->desde
            : $this->secuencia_actual + 1;
        $this->save();
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_ecf', $tipo);
    }

    public function scopeDisponibles($query)
    {
        return $query->whereColumn('secuencia_actual', '<', 'hasta')
                     ->where('estado', true);
    }

    // ==========================================
    // Helper: tipos e-CF según DGII
    // ==========================================

    public static function tiposEcf(): array
    {
        return [
            '31' => 'E31 - Factura de Crédito Fiscal',
            '32' => 'E32 - Factura de Consumidor Final',
            '33' => 'E33 - Nota de Débito',
            '34' => 'E34 - Nota de Crédito',
            '41' => 'E41 - Comprobante de Compra',
            '43' => 'E43 - Gastos Menores',
            '44' => 'E44 - Regímenes Especiales',
            '45' => 'E45 - Comprobante Gubernamental',
            '46' => 'E46 - Exportaciones',
            '47' => 'E47 - Pagos al Exterior',
        ];
    }
}