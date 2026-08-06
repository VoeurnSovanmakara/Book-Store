<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Pagination\LengthAwarePaginator;

class CouponService
{
    public function list(int $perPage = 10): LengthAwarePaginator
    {
        return Coupon::query()->latest()->paginate($perPage);
    }

    public function create(array $data): Coupon
    {
        return Coupon::create($data);
    }

    public function update(Coupon $coupon, array $data): Coupon
    {
        $coupon->update($data);
        return $coupon;
    }

    public function delete(Coupon $coupon): void
    {
        $coupon->delete();
    }

    public function validate(string $code): array
    {
        $coupon = Coupon::where('code', $code)->first();

        if (! $coupon) {
            return ['valid' => false, 'reason' => 'Coupon code not found.'];
        }

        if (! $coupon->isValid()) {
            $reason = match (true) {
                $coupon->used_count >= $coupon->limit_count => 'This coupon has reached its usage limit.',
                now()->lt($coupon->effective_date) => 'This coupon is not yet active.',
                now()->gt($coupon->expired_date) => 'This coupon has expired.',
                default => 'This coupon is invalid.'
            };

            return ['valid' => false, 'reason' => $reason];
        }

        return ['valid' => true, 'coupon' => $coupon];
    }
}
