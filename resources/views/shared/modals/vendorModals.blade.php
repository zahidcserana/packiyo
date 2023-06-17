@include('shared.modals.components.vendor.create')

<div class="modal fade confirm-dialog" id="vendorEditModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

<div class="modal fade confirm-dialog" id="vendorDeleteModal" role="dialog">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content bg-white">
            @include('shared.modals.components.vendor.delete')
        </div>
    </div>
</div>
