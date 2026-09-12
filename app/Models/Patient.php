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

    // ✅ Accessor para nombre completo
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Relaciones
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



    // En app/Models/Patient.php

public function clinicalRecords()
{
    return $this->hasMany(ClinicalRecord::class)->orderBy('consultation_date', 'desc');
}


public function getAgeAttribute()
{
    if ($this->birth_date) {
        return \Carbon\Carbon::parse($this->birth_date)->age;
    }
    return null;
}
}