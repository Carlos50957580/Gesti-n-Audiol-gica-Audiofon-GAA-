<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'branch_id', 'quantity', 'reserved_quantity'
    ];

    protected $casts = [
        'quantity'          => 'integer',
        'reserved_quantity' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getAvailableQuantityAttribute()
    {
        return max(0, $this->quantity - $this->reserved_quantity);
    }
}