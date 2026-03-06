<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'audiologist_id',
        'appointment_date',
        'appointment_time',
        'status',
        'notes',
        'branch_id'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function audiologist()
    {
        return $this->belongsTo(User::class,'audiologist_id');
    }

    public function branch()
{
    return $this->belongsTo(Branch::class);
}
}