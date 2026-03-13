<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    // Una sucursal tiene muchos usuarios
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Una sucursal tiene muchas citas
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // Una sucursal tiene muchos pacientes
    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    // Una sucursal tiene muchas facturas
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    
}