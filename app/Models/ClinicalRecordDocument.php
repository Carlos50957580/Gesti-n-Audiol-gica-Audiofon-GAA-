<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'uploaded_by',
    ];

    // ── Relaciones ────────────────────────────────────────
    public function clinicalRecord()
    {
        return $this->belongsTo(ClinicalRecord::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ── Helpers ───────────────────────────────────────────
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes < 1024)       return $bytes . ' B';
        if ($bytes < 1048576)    return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    public function getFileIconAttribute(): string
    {
        return match(strtolower($this->file_type)) {
            'pdf'  => 'ri-file-pdf-line',
            'doc', 'docx' => 'ri-file-word-line',
            default => 'ri-file-line',
        };
    }

    public function getFileIconColorAttribute(): string
    {
        return match(strtolower($this->file_type)) {
            'pdf'  => 'text-danger',
            'doc', 'docx' => 'text-primary',
            default => 'text-secondary',
        };
    }
}