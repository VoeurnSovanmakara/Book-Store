@component('mail::message')
# Daily Sales Report

**Date:** {{ $summary['date'] }}

@component('mail::table')
| Metric | Value |
|:-------|------:|
| Total Orders | {{ $summary['total_orders'] }} |
| Total Revenue | ${{ number_format($summary['total_revenue'], 2) }} |
@endcomponent

@component('mail::button', ['url' => config('app.url')])
View Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent