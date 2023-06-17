@include('shared.forms.input', [
    'name' => 'name',
    'label' => __('Location'),
    'value' => $location->name ?? ''
])
@include('shared.forms.checkbox', [
   'name' => 'pickable',
   'label' => __('Pickable'),
   'checked' => $location->pickable ?? 0
    === 1 ? 1 : 0,
])
@include('shared.forms.checkbox', [
   'name' => 'disabled_on_picking_app',
   'label' => __('Disabled on picking app'),
   'checked' => $location->disabled_on_picking_app ?? 0
    === 1 ? 1 : 0,
])
@include('shared.forms.checkbox', [
   'name' => 'priority_counting_requested_at',
   'label' => __('Priority Counting'),
   'checked' => $location->priority_counting_requested_at ?? 0
    === 1 ? 1 : 0,
])
