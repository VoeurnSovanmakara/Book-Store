<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'code',
        'amount',
        'limit_count',
        'used_count',
        'effective_date',
        'expired_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'effective_date' => 'date',
        'expired_date' => 'date',
    ];

    public function isValid(): bool
    {
        $today = now()->toDateString();

        return $this->effective_date->lte($today)
            && $this->expired_date->gte($today)
            && $this->used_count < $this->limit_count;
    }
}
