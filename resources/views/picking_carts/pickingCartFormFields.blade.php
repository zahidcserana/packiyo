@include('shared.forms.ajaxSelect', [
    'url' => route('pickingCart.filterWarehouses'),
    'name' => 'warehouse_id',
    'className' => 'ajax-user-input',
    'placeholder' => __('Search'),
    'label' => __('Warehouse'),
    'default' => [
         'id' => $cart->warehouse->id ?? '',
         'text' => $cart->warehouse->contactInformation->name ?? ''
     ]
])
@include('shared.forms.input', [
   'name' => 'name',
   'label' => __('Name'),
   'value' => $cart->name ?? $name ?? ''
])
@include('shared.forms.input', [
  'name' => 'number_of_totes',
  'label' => __('Number of totes/shelves'),
  'value' => $cart->number_of_totes ?? 0,
  'type' => 'number'
])
