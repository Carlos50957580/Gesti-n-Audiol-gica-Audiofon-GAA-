<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{

    protected $fillable = [
        'name',
        'address',
        'email',
        'phone',
        'authorization_phone',
        'coverage_percentage',
        'active'
    ];

  
    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function invoices()
{
    return $this->hasMany(Invoice::class);
}
}