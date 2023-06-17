@if (isset($sessionCustomer))
    <input type="hidden" id="customer_id" name="customer_id" value="{{ $sessionCustomer->id }}" class="customer_id" />
@else
    @include('shared.forms.ajaxSelect', [
        'url' => $route,
        'name' => 'customer_id',
        'id' => 'customer_id',
        'className' => 'ajax-user-input customer_id',
        'readonly' => $readonly ?? null,
        'placeholder' => __('Search'),
        'label' => __('Customer'),
        'default' => [
            'id' => $id,
            'text' => $text
        ]
    ])
@endif
