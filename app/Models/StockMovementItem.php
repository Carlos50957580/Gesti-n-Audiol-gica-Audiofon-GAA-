<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovementItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'movement_id', 'product_id', 'quantity', 'unit_price',
        'subtotal', 'previous_stock', 'new_stock', 'notes'
    ];

    protected $casts = [
        'quantity'       => 'integer',
        'unit_price'     => 'decimal:2',
        'subtotal'       => 'decimal:2',
        'previous_stock' => 'integer',
        'new_stock'      => 'integer',
    ];

    public function movement()
    {
        return $this->belongsTo(StockMovement::class, 'movement_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}