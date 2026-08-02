<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',      // ✅ Cambiado de audiologist_id a doctor_id
        'appointment_date',
        'appointment_time',
        'status',
        'notes',
        'branch_id'
    ];

    // Relaciones
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()  // ✅ Cambiado de audiologist a doctor
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    // Scopes
    public function scopeProgramada($query)
    {
        return $query->where('status', 'programada');
    }

    public function scopeCompletada($query)
    {
        return $query->where('status', 'completada');
    }

    public function scopeCancelada($query)
    {
        return $query->where('status', 'cancelada');
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }
}