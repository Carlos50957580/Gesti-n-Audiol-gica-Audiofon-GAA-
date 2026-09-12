<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference', 'type', 'branch_id', 'supplier_id', 'user_id',
        'destination_branch_id', 'movement_date', 'notes', 'total', 'status'
    ];

    protected $casts = [
        'movement_date' => 'date',
        'total'         => 'decimal:2',
    ];

    // ── Relaciones ─────────────────────────────────────────────
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function destinationBranch()
    {
        return $this->belongsTo(Branch::class, 'destination_branch_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(StockMovementItem::class, 'movement_id');
    }

    // ── Accessors ──────────────────────────────────────────────
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'entrada'       => 'Entrada',
            'salida'        => 'Salida',
            'ajuste'        => 'Ajuste',
            'transferencia' => 'Transferencia',
            default         => $this->type,
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'borrador'   => 'Borrador',
            'confirmado' => 'Confirmado',
            'cancelado'  => 'Cancelado',
            default      => $this->status,
        };
    }

    // ── Helpers ────────────────────────────────────────────────
    public function isConfirmed()
    {
        return $this->status === 'confirmado';
    }

    public function isDraft()
    {
        return $this->status === 'borrador';
    }
}