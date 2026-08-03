<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalRecordDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinical_record_id',
        'patient_id',
        'name',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'mime_type',
        'uploaded_by',
        'description',
    ];

    // ============================================
    // RELACIONES
    // ============================================
    
    public function clinicalRecord()
    {
        return $this->belongsTo(ClinicalRecord::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ============================================
    // ACCESORES
    // ============================================
    
    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}