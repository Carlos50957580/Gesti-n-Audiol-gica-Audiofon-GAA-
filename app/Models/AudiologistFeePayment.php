<?php
// app/Models/AudiologistFeePayment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudiologistFeePayment extends Model
{
    use HasFactory;

    protected $table = 'audiologist_fee_payments';

    protected $fillable = [
        'audiologist_id',
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

    public function audiologist()
    {
        return $this->belongsTo(User::class, 'audiologist_id');
    }

    public function fees()
    {
        return $this->belongsToMany(
            AudiologistFee::class,
            'audiologist_fee_payment_details',
            'payment_id',
            'fee_id'
        )->withPivot('amount_applied')->withTimestamps();
    }
}