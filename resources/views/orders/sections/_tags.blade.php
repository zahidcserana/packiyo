@include('shared.forms.editSelectTag', [
    'containerClass' => 'd-flex justify-content-between py-2 align-items-center',
    'labelClass' => '',
    'selectClass' => 'select-ajax-tags',
    'label' => '',
    'minimumInputLength' => 3,
    'default' => $order->tags ?? null
])
