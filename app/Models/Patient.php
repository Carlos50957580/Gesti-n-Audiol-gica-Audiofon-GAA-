<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'cedula',
        'phone',
        'email',
        'birth_date',
        'gender',
        'address',
        'branch_id',
        'insurance_id', 
        'insurance_number'  
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function insurance()
    {
        return $this->belongsTo(Insurance::class);
    }

    public function invoices()
{
    return $this->hasMany(Invoice::class);
}

public function clinicalRecords()
{
    return $this->hasMany(ClinicalRecord::class);
}
}