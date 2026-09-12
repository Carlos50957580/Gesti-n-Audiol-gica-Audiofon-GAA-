<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_invoice_id', 'product_id', 'quantity', 'price',
        'subtotal', 'tax_amount', 'total_with_tax',
    ];

    protected $casts = [
        'quantity'       => 'integer',
        'price'          => 'decimal:2',
        'subtotal'       => 'decimal:2',
        'tax_amount'     => 'decimal:2',
        'total_with_tax' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(ProductInvoice::class, 'product_invoice_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}