<div class="modal-content" id="create-edit-modal">
    <div class="modal-header border-bottom mx-4 px-0">
        <h6 class="modal-title text-black text-left">
            @if ($returnStatus)
                Edit Return status: {{ $returnStatus->name }}
            @else
                Create Return status
            @endif
        </h6>

        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
            <span aria-hidden="true" class="text-black">&times;</span>
        </button>
    </div>

    <div class="modal-body">
        <form
            method="POST"
            @if ($returnStatus)
                action="{{ route('return_status.update', ['return_status' => $returnStatus]) }}"
            @else
                action="{{ route('return_status.store') }}"
            @endif
            autocomplete="off"
            id="create-edit-form"
        >
            @csrf
            @if ($returnStatus)
                @method('PUT')
            @endif

            <div class="pl-lg-4">
                @include('return_status.returnStatusInformationFields', [
                    '$returnStatus' => $returnStatus
                ])
                <div class="text-center">
                    <button type="submit" class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 mt-5">{{ __('Save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        const createEditModal = $("#create-edit-modal")

        createEditModal.find('.customer_id').select2({
            dropdownParent: createEditModal
        })
    })
</script>
