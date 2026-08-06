<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'customer_id', 
        'customer_address_id', 
        'coupon_id',
        'sub_total_price', 
        'discount', 
        'total_payable', 
        'status',
    ];

    protected $casts = [
        'sub_total_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_payable' => 'decimal:2'
    ];


    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'customer_address_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }
}

