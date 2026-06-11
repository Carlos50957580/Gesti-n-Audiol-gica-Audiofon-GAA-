<?php
// app/Models/AudiologistFeeSetting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudiologistFeeSetting extends Model
{
    use HasFactory;

    protected $table = 'audiologist_fees_settings';

    protected $fillable = [
        'audiologist_id',
        'calculation_type',
        'value',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function audiologist()
    {
        return $this->belongsTo(User::class, 'audiologist_id');
    }

    // Helper para calcular honorario
    public function calculateFee($invoiceTotal)
    {
        if ($this->calculation_type === 'percentage') {
            return $invoiceTotal * ($this->value / 100);
        }
        
        return min($this->value, $invoiceTotal); // El monto fijo no puede superar el total
    }
}