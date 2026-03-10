<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [

        'patient_id',
        'user_id',
        'branch_id',
        'insurance_id',

        'subtotal',
        'insurance_discount',
        'total',

        'status',
        'authorization_number'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function insurance()
    {
        return $this->belongsTo(Insurance::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

}