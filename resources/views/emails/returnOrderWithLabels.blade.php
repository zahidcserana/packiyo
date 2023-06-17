@component('mail::message', ['headerImage' => $headerImage])

# {{ __('Return order label') }}

**{{ __('To download a return labels for order number #:number click link below:', ['number' => $return->order->number]) }}**

@foreach ($returnLabels as $returnLabel)
@component('mail::button', [
    'url' => $returnLabel['url'] ?? '',
    'color' => 'orange'
])
{{ $returnLabel['name'] }}
@endcomponent
@endforeach

**{{ __('If you did not expect to receive this e-mail from us, please delete it.') }}**

@component('mail::footer')
**{{ __('Questions?') }}**

**[support@packiyo.com](mailto:{{ config('mail.support_mail') }})**
@endcomponent

@endcomponent

