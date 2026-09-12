<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'code', 'barcode', 'name', 'description', 'unit',
        'cost_price', 'sale_price', 'min_stock', 'max_stock',
        'has_tax', 'is_active', 'image'
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'has_tax'    => 'boolean',
        'is_active'  => 'boolean',
    ];

    // ── Relaciones ─────────────────────────────────────────────
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier')
                    ->withPivot('supplier_code', 'last_cost')
                    ->withTimestamps();
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function movementItems()
    {
        return $this->hasMany(StockMovementItem::class);
    }

    // ── Scopes ─────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query, $branchId = null)
    {
        return $query->whereHas('stocks', function ($q) use ($branchId) {
            if ($branchId) {
                $q->where('branch_id', $branchId);
            }
            $q->whereColumn('quantity', '<=', 'products.min_stock');
        });
    }

    // ── Accessors ──────────────────────────────────────────────
    public function getImageUrlAttribute()
    {
        return $this->image 
            ? asset('storage/' . $this->image) 
            : asset('velzon/assets/images/products/default.png');
    }

    public function getTotalStockAttribute()
    {
        return $this->stocks->sum('quantity');
    }

    public function getStockInBranchAttribute($branchId)
    {
        return $this->stocks->where('branch_id', $branchId)->first()?->quantity ?? 0;
    }

    // ── Helper Methods ─────────────────────────────────────────
    public function getStockForBranch($branchId)
    {
        return $this->stocks()->where('branch_id', $branchId)->first();
    }

    public function hasLowStock($branchId = null)
    {
        $query = $this->stocks();
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        return $query->whereColumn('quantity', '<=', 'products.min_stock')->exists();
    }
}