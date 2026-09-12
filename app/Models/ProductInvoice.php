<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Setting;

class ProductInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'patient_id', 'user_id', 'branch_id',
        'subtotal', 'tax_amount', 'total_with_tax', 'discount', 'total',
        'status', 'with_ncf', 'ncf', 'ncf_type',
        'customer_rnc', 'customer_business_name', 'notes',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'total_with_tax'  => 'decimal:2',
        'discount'        => 'decimal:2',
        'total'           => 'decimal:2',
        'with_ncf'        => 'boolean',
    ];

    // ── Relaciones ──────────────────────────────────────────
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

    public function items()
    {
        return $this->hasMany(ProductInvoiceItem::class);
    }

    public function receipts()
    {
        return $this->hasMany(ProductReceipt::class);
    }

    public function receipt()
    {
        return $this->hasOne(ProductReceipt::class);
    }

    // ── Accessors ───────────────────────────────────────────
    public function getFormattedTotalAttribute()
    {
        return 'RD$ ' . number_format($this->total, 2);
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pendiente' => 'Pendiente',
            'pagada'    => 'Pagada',
            'cancelada' => 'Cancelada',
            default     => $this->status,
        };
    }

    // ── Helpers ─────────────────────────────────────────────
    public static function generateNumber(): string
    {
        $prefix = 'FAC-PROD-';
        $last = self::where('number', 'like', $prefix . '%')
                    ->orderByDesc('id')->first();
        $next = $last ? ((int) substr($last->number, -6)) + 1 : 1;
        return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    public function isPaid()
    {
        return $this->status === 'pagada';
    }

    public function canBeCancelled()
    {
        return $this->status === 'pendiente';
    }
}