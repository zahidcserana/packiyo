<div class="modal fade confirm-dialog" id="bulk-edit-modal" role="dialog">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content bg-white">
            <form method="post" action="" autocomplete="off" class="modal-content" id="bulk-edit-form">
                @csrf
                <div class="modal-header border-bottom mx-4 px-0">
                    <h6 class="modal-title text-black text-left" id="modal-title-notification">{{ __('Bulk Edit') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true" class="text-black">&times;</span>
                    </button>
                </div>
                <input type="hidden" name="ids" id="model-ids">
                <div class="modal-body text-center py-3 overflow-auto">
                    <div class="select-tags">
                        <label for="tags"
                               class="text-neutral-text-gray font-weight-600 font-xs" data-id="tags">{{ __('Tags') }}
                        </label>
                        <input type="hidden" name="tags" value="">
                        <select name="tags[]" id="bulkEditTags"
                                data-ajax--url="{{ route('tag.filterInputTags') }}"
                                data-minimum-input-length="3"
                                data-toggle="select"
                                data-dropdown-parent="#bulk-edit-modal"
                                multiple
                        >
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="submit-bulk-edit" class="btn bg-logoOrange mx-auto px-5 text-white">{{ __('Save on all') }} <span id="number-of-selected-items"></span> <span id="item-type"></span></button>
                </div>
            </form>
        </div>
    </div>
</div>
