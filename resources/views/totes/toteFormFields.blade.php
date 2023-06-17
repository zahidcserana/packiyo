@include('shared.forms.ajaxSelect', [
    'url' => route('tote.filterWarehouses'),
    'name' => 'warehouse_id',
    'className' => 'ajax-user-input',
    'placeholder' => __('Search'),
    'label' => __('Warehouse'),
    'default' => [
         'id' => $tote->warehouse->id ?? '',
         'text' => $tote->warehouse->contactInformation->name ?? ''
     ]
])
@if($createForm)
    @include('shared.forms.input', [
       'name' => 'name_prefix',
       'label' => __('Name prefix'),
       'value' => \App\Components\ToteComponent::TOTE_PREFIX
    ])
    @include('shared.forms.input', [
      'name' => 'number_of_totes',
      'label' => __('Number of totes'),
      'value' => 1,
      'type' => 'number'
    ])
@else
    @include('shared.forms.ajaxSelect', [
        'url' => route('tote.filterPickingCarts'),
        'name' => 'picking_cart_id',
        'className' => 'ajax-user-input',
        'placeholder' => __('Search'),
        'label' => __('Picking Cart'),
        'default' => [
             'id' => $tote->pickingCart->id ?? '',
             'text' => $tote->pickingCart->name ?? ''
         ]
    ])
    @include('shared.forms.input', [
       'name' => 'name',
       'label' => __('Name'),
       'value' => $tote->name ?? $name
    ])
@endif
