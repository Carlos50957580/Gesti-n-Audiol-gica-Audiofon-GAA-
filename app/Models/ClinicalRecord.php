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
        'doctor_id',
        'branch_id',
        'reason_for_consultation',
        'diagnosis',
        'treatment_plan',
        'notes',
        'status',
        'anamnesis',
        'vital_signs',
        'physical_exam',
        'presumptive_diagnosis',
        'treatment',
        'evolution',
        'observations',
        'recommendations',
        'consultation_date',
        'consultation_type',
        'consultation_reason',
    ];

    protected $casts = [
        'vital_signs' => 'array',
        'consultation_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================
    // RELACIONES
    // ============================================
    
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function documents()
    {
        return $this->hasMany(ClinicalRecordDocument::class);
    }

    // ============================================
    // SCOPES
    // ============================================
    
    public function scopePending($query)
    {
        return $query->where('status', 'pendiente');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completada');
    }

    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    // ============================================
    // ACCESORES Y MUTADORES
    // ============================================
    
    public function getVitalSignsAttribute($value)
    {
        $defaults = [
            'blood_pressure_systolic' => null,
            'blood_pressure_diastolic' => null,
            'heart_rate' => null,
            'respiratory_rate' => null,
            'oxygen_saturation' => null,
            'temperature' => null,
            'weight' => null,
            'height' => null,
            'bmi' => null,
            'fetal_heart_rate' => null,
            'uterine_height' => null,
            'edema' => null,
            'fetal_movements' => null,
        ];
        
        if (empty($value)) {
            return $defaults;
        }
        
        $decoded = json_decode($value, true);
        return array_merge($defaults, $decoded ?? []);
    }

    public function setVitalSignsAttribute($value)
    {
        $this->attributes['vital_signs'] = json_encode($value);
    }

    public function getFormattedVitalSignsAttribute()
    {
        $signs = $this->vital_signs;
        $formatted = [];
        
        if ($signs['blood_pressure_systolic'] && $signs['blood_pressure_diastolic']) {
            $formatted['Presión Arterial'] = "{$signs['blood_pressure_systolic']}/{$signs['blood_pressure_diastolic']} mmHg";
        }
        
        if ($signs['heart_rate']) {
            $formatted['Frecuencia Cardíaca'] = "{$signs['heart_rate']} lpm";
        }
        
        if ($signs['respiratory_rate']) {
            $formatted['Frecuencia Respiratoria'] = "{$signs['respiratory_rate']} rpm";
        }
        
        if ($signs['oxygen_saturation']) {
            $formatted['Saturación de Oxígeno'] = "{$signs['oxygen_saturation']}%";
        }
        
        if ($signs['temperature']) {
            $formatted['Temperatura'] = "{$signs['temperature']} °C";
        }
        
        if ($signs['weight']) {
            $formatted['Peso'] = "{$signs['weight']} kg";
        }
        
        if ($signs['height']) {
            $formatted['Talla'] = "{$signs['height']} cm";
        }
        
        if ($signs['bmi']) {
            $formatted['IMC'] = number_format($signs['bmi'], 2);
        }
        
        if ($signs['fetal_heart_rate']) {
            $formatted['FC Fetal'] = "{$signs['fetal_heart_rate']} lpm";
        }
        
        if ($signs['uterine_height']) {
            $formatted['Altura Uterina'] = "{$signs['uterine_height']} cm";
        }
        
        if ($signs['edema']) {
            $formatted['Edema'] = $signs['edema'];
        }
        
        if ($signs['fetal_movements']) {
            $formatted['Movimientos Fetales'] = $signs['fetal_movements'];
        }
        
        return $formatted;
    }

    public function getConsultationTypeLabelAttribute()
    {
        $types = [
            'primera_vez' => 'Primera Vez',
            'seguimiento' => 'Seguimiento',
            'urgencia' => 'Urgencia',
            'control' => 'Control',
        ];
        
        return $types[$this->consultation_type] ?? $this->consultation_type;
    }

    public function getStatusLabelAttribute()
    {
        $statuses = [
            'pendiente' => 'Pendiente',
            'completada' => 'Completada',
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }

    // ============================================
    // MÉTODOS DE UTILIDAD
    // ============================================
    
    public function canBeEdited()
    {
        return $this->status === 'pendiente';
    }

    public function calculateBMI($weight, $height)
    {
        if ($weight && $height && $height > 0) {
            $heightInMeters = $height / 100;
            return round($weight / ($heightInMeters * $heightInMeters), 2);
        }
        return null;
    }
}