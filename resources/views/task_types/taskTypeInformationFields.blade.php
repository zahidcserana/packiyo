@include('shared.forms.input', [
    'name' => 'name',
    'label' => __('Name'),
    'value' => $taskType->name ?? ''
])
@include('shared.forms.dropdowns.customer_selection', [
    'route' => route('task_type.filterCustomers'),
    'readonly' => isset($taskType->customer->id) ? 'true' : null,
    'id' => $taskType->customer->id ?? old('customer_id'),
    'text' => $taskType->customer->contactInformation->name ?? ''
])
