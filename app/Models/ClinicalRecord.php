<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
    'invoice_id',
    'patient_id',
    'audiologist_id',
    'branch_id',
    'reason_for_consultation',
    'diagnosis',
    'treatment_plan',
    'notes',
    'status',
];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function audiologist()
    {
        return $this->belongsTo(User::class, 'audiologist_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pendiente'  => '<span class="badge bg-warning-subtle text-warning">Pendiente</span>',
            'completada' => '<span class="badge bg-success-subtle text-success">Completada</span>',
            default      => '<span class="badge bg-secondary">Desconocido</span>',
        };
    }
}