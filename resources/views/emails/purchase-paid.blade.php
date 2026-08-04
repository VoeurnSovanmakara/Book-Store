@component('mail::message')
# Payment Confirmed

Hi **{{ $purchase->customer->name }}**, your payment for order **#{{ $purchase->id }}** has been confirmed.

**Total Paid:** ${{ number_format($purchase->total_payable, 2) }}

Shipping to: {{ $purchase->address->detail }}

@component('mail::button', ['url' => config('app.url')])
View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent