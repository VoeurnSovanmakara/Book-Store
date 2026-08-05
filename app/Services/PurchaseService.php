<?php

namespace App\Services;

use App\Events\PurchasePaid;
use App\Jobs\SendPurchaseConfirmationEmail;
use App\Models\Book;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PurchaseService
{
    private const ALLOWED_TRANSITIONS = [
        'pending' => ['paid', 'cancelled'],
        'paid' => ['cancelled'],
        'cancelled' => [],
    ];

    public function create(Customer $customer, array $data): Purchase
    {
        $address = CustomerAddress::find($data['customer_address_id']);

        if ($address->customer_id !== $customer->id) {
            throw ValidationException::withMessages([
                'customer_address_id' => ['This address does not belong to you.'],
            ]);
        }

        return DB::transaction(function() use ($customer, $address, $data) {
            $books = Book::whereIn('id', collect($data['items'])->pluck('book_id'))
                ->get()
                ->keyBy('id');

            $subTotal = 0;
            $lineItems = [];


            foreach ($data['items'] as $item) {
                $book = $books[$item['book_id']];
                $lineTotal = $book->price * $item['qty'];
                $subTotal += $lineTotal;

                $lineItems[] = [
                    'book_id' => $book->id,
                    'qty' => $item['qty'],
                    'price' => $book->price,
                ];
            }

            $discount = $this->resolveDiscount($data['coupon_code'] ?? null, $subTotal);

            $purchase = Purchase::create([
                'customer_id' => $customer->id,
                'customer_address_id' => $address->id,
                'sub_total_price' => $subTotal,
                'discount' => $discount,
                'total_payable' => max($subTotal - $discount, 0),
                'status' => 'pending',
            ]);

            $purchase->details()->createMany($lineItems);

            if (! empty($data['coupon_code'])) {
                Coupon::where('code', $data['coupon_code'])->increment('used_count');
            }

            $purchase = $purchase->load('details.book', 'address');

            Log::channel('purchases')->info('Purchase created', [
                'purchase_id' => $purchase->id,
                'customer_id' => $customer->id,
                'total_payable' => $purchase->total_payable,
                'coupon_code' => $data['coupon_code'] ?? null,
            ]);

            SendPurchaseConfirmationEmail::dispatch($purchase);

            return $purchase;
        });
    }

    private function resolveDiscount(?string $couponCode, float $subTotal): float
    {
        if (! $couponCode) {
            return 0;
        }

        $coupon = Coupon::where('code', $couponCode)->first();

        if (! $coupon || ! $coupon->isValid()) {
            throw ValidationException::withMessages([
                'coupon_code' => ['This coupon is invalid or expired.'],
            ]);
        }

        // discount never exceeds the subtotal itself
        return min($coupon->amount, $subTotal);
    }

    public function updateStatus(Purchase $purchase, string $newStatus): Purchase
    {
        $allowed = self::ALLOWED_TRANSITIONS[$purchase->status] ?? [];

        if (! in_array($newStatus, $allowed, true)) {
            throw ValidationException::withMessages([
                'status' => ["Cannot change status from \"{$purchase->status}\" to \"{$newStatus}\"."],
            ]);
        }

        $oldStatus = $purchase->status;
        $purchase->update(['status' => $newStatus]);

        Log::channel('purchases')->info('Purchase status changed', [
            'purchase_id' => $purchase->id,
            'from' => $oldStatus,
            'to' => $newStatus,
        ]);

        if ($newStatus === 'paid') {
            event(new PurchasePaid($purchase));
        }

        return $purchase;
    }
}