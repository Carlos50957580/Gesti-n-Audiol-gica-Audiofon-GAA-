<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'user_id',
        'branch_id',
        'insurance_id',
        'subtotal',
        'insurance_discount',
        'total',
        'status',
        'authorization_number',
        'audiologist_id',
    ];

    protected $casts = [
        'subtotal'           => 'decimal:2',
        'insurance_discount' => 'decimal:2',
        'total'              => 'decimal:2',
    ];

    // ── Relations ────────────────────────────────────────────────────────────


    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function insurance()
    {
        return $this->belongsTo(Insurance::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function clinicalRecord()
{
    return $this->hasOne(ClinicalRecord::class);
}

public function audiologist()
{
    return $this->belongsTo(User::class, 'audiologist_id');
}
    // ── Helpers ──────────────────────────────────────────────────────────────

    public function getInvoiceNumberAttribute(): string
    {
        return 'FAC-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pendiente'  => '<span class="badge bg-warning-subtle text-warning">Pendiente</span>',
            'pagada'     => '<span class="badge bg-success-subtle text-success">Pagada</span>',
            'cancelada'  => '<span class="badge bg-danger-subtle text-danger">Cancelada</span>',
            default      => '<span class="badge bg-secondary">Desconocido</span>',
        };
    }
}