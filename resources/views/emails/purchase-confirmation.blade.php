@component('mail::message')
# Order Confirmation

Thanks for your order, **{{ $purchase->customer->name }}**!

**Order #{{ $purchase->id }}**

@component('mail::table')
| Book | Qty | Price | Line Total |
|:-----|:---:|------:|-----------:|
@foreach($purchase->details as $item)
| {{ $item->book->title }} | {{ $item->qty }} | ${{ number_format($item->price, 2) }} | ${{ number_format($item->price * $item->qty, 2) }} |
@endforeach
@endcomponent

**Subtotal:** ${{ number_format($purchase->sub_total_price, 2) }}
**Discount:** -${{ number_format($purchase->discount, 2) }}
**Total:** ${{ number_format($purchase->total_payable, 2) }}

Shipping to: {{ $purchase->address->detail }}

@component('mail::button', ['url' => config('app.url')])
View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent