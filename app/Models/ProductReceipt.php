<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'product_invoice_id', 'user_id', 'branch_id',
        'cash_amount', 'card_amount', 'transfer_amount', 'total_paid',
        'card_reference', 'transfer_reference', 'notes',
    ];

    protected $casts = [
        'cash_amount'     => 'decimal:2',
        'card_amount'     => 'decimal:2',
        'transfer_amount' => 'decimal:2',
        'total_paid'      => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(ProductInvoice::class, 'product_invoice_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getPaymentSummaryAttribute()
    {
        $methods = [];
        if ($this->cash_amount > 0)     $methods[] = 'Efectivo';
        if ($this->card_amount > 0)     $methods[] = 'Tarjeta';
        if ($this->transfer_amount > 0) $methods[] = 'Transferencia';
        return implode(' + ', $methods) ?: 'Sin pago';
    }

    public static function generateNumber(): string
    {
        $prefix = 'REC-PROD-';
        $last = self::where('number', 'like', $prefix . '%')
                    ->orderByDesc('id')->first();
        $next = $last ? ((int) substr($last->number, -6)) + 1 : 1;
        return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}