@component('mail::message')
# Batch Shipped

Your batch has been shipped!

@component('mail::table')
| **Batch ID**   | **{{ $bulkShipBatch->id }}**                          |
| :------------- | :---------------------------------------------------- |
| Orders shipped | {{ $bulkShipBatch->orders->count() }}                 |
| Created at     | {{ $bulkShipBatch->created_at->format('d/m/Y H:i') }} |
| Shipped at     | {{ ($bulkShipBatch->shipped_at ?? now())->format('d/m/Y H:i') }} |
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
