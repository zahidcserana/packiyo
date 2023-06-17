<div class="modal fade confirm-dialog" id="order-return-modal" role="dialog" data-id="{{ $order->id }}" {{ $dataKeyboard ?? '' ? 'data-backdrop=static data-keyboard=false' : '' }}>
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body overflow-auto p-5"></div>
        </div>
    </div>
</div>
@include('shared.modals.shippingInformationEdit', [
    'saveBtnClass' => 'float-right',
    'dataKeyboard' => false,
])
