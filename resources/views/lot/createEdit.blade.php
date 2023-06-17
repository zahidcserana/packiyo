<div class="modal-content" id="create-edit-modal">
    <div class="modal-header border-bottom mx-4 px-0">
        <h6 class="modal-title text-black text-left">
            @if ($lot)
                Edit Lot: {{ $lot->name }}
            @else
                Create Lot
            @endif
        </h6>

        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
            <span aria-hidden="true" class="text-black">&times;</span>
        </button>
    </div>

    <div class="modal-body">
        <form
            method="POST"
            @if ($lot)
                action="{{ route('lot.update', ['lot' => $lot]) }}"
            @else
                action="{{ route('lot.store') }}"
            @endif
            autocomplete="off"
            id="create-edit-form"
        >
            @csrf
            @if ($lot)
                @method('PUT')
            @endif

            <div class="pl-lg-4">
                @include('lot.lotInformationFields', [
                    '$lot' => $lot
                ])
                <div class="text-center">
                    <button type="submit" class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 mt-5">{{ __('Save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
