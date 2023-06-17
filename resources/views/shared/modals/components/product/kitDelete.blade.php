<div class="modal-content p-3" id="productKitDelete">
    <form action="{{ route('product.removeKitProduct', [$product->id, $parentId]) }}" method="POST" id="deleteKitProductForm">
        @csrf
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                <span aria-hidden="true" class="text-black">&times;</span>
            </button>
        </div>
        <div class="modal-body text-black py-3 overflow-auto">
            Are you sure you want to remove this component from this kit?
        </div>
        <div class="modal-footer">
            <div class="justify-content-around d-flex w-100">
                <button type="submit" class="btn mx-auto px-5 text-black" data-dismiss="modal" id="deleteKitProduct" >Cancel</button>
                <button type="submit" class="btn bg-logoOrange mx-auto px-5 text-white">Ok</button>
            </div>
        </div>
    </form>
</div>
