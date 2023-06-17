@include('shared.modals.components.purchase_orders.create')

<div class="modal fade confirm-dialog" id="purchaseOrderEditModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

<div class="modal fade confirm-dialog" id="purchaseOrderDeleteModal" role="dialog">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content bg-white">
            @include('shared.modals.components.purchase_orders.delete')
        </div>
    </div>
</div>

<div class="modal fade confirm-dialog" id="closePurchaseOrderModal" role="dialog">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content bg-white">
            @include('shared.modals.components.purchase_orders.close')
        </div>
    </div>
</div>

@include('shared.modals.components.purchase_orders.importCsv')
@include('shared.modals.components.purchase_orders.exportCsv')
