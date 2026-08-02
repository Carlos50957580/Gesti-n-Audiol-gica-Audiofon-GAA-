<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorFeePayment extends Model
{
    use HasFactory;

    protected $table = 'doctor_fee_payments';

    protected $fillable = [
        'doctor_id',
        'amount',
        'payment_date',
        'reference_number',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function fees()
    {
        return $this->belongsToMany(
            DoctorFee::class,
            'doctor_fee_payment_details',
            'doctor_fee_payment_id',
            'doctor_fee_id'
        )->withPivot('amount_applied')->withTimestamps();
    }
}