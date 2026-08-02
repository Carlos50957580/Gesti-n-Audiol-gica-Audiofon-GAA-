<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorFee extends Model
{
    use HasFactory;

    protected $table = 'doctor_fees';

    protected $fillable = [
        'doctor_id',
        'invoice_id',
        'invoice_total',
        'calculation_type',
        'calculation_value',
        'fee_amount',
        'status',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        'invoice_total' => 'decimal:2',
        'calculation_value' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function payments()
    {
        return $this->belongsToMany(
            DoctorFeePayment::class,
            'doctor_fee_payment_details',
            'doctor_fee_id',
            'doctor_fee_payment_id'
        )->withPivot('amount_applied')->withTimestamps();
    }

    public function getPaidAmountAttribute()
    {
        return $this->payments()->sum('amount_applied');
    }

    public function getRemainingAmountAttribute()
    {
        return $this->fee_amount - $this->paid_amount;
    }

    public function getIsFullyPaidAttribute()
    {
        return $this->remaining_amount <= 0;
    }
}