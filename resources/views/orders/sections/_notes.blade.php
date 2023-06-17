<textarea name="note_text_append" class="form-control text-black"></textarea>

<div class="d-lg-flex d-sm-block justify-content-lg-between p-2">
    @include('shared.forms.typeRadio', [
        'value' => 'packing_note',
        'name' => 'note_type_append',
        'containerClass' => 'custom-radio',
        'label' => __('Note to packer'),
        'labelClass' => 'custom-control-label',
        'inputTypeClass' => 'custom-control-input',
        'inputID' => 'noteToPacker',
        'checked' => true,
    ])
    @include('shared.forms.typeRadio', [
        'value' => 'slip_note',
        'name' => 'note_type_append',
        'containerClass' => 'custom-radio',
        'label' => __('Slip note'),
        'labelClass' => 'custom-control-label',
        'inputTypeClass' => 'custom-control-input',
        'inputID' => 'slipNote'
    ])
    @include('shared.forms.typeRadio', [
        'value' => 'gift_note',
        'name' => 'note_type_append',
        'containerClass' => 'custom-radio',
        'label' => __('Gift note'),
        'labelClass' => 'custom-control-label',
        'inputTypeClass' => 'custom-control-input',
        'inputID' => 'giftNote'
    ])
    @include('shared.forms.typeRadio', [
        'value' => 'internal_note',
        'name' => 'note_type_append',
        'containerClass' => 'custom-radio',
        'label' => __('Internal note'),
        'labelClass' => 'custom-control-label',
        'inputTypeClass' => 'custom-control-input',
        'inputID' => 'internalNote'
    ])
    <div class="d-flex justify-content-end">
        <a href="#" class="save-changes save-notes btn bg-logoOrange text-white px-3 py-2 font-weight-700 border-8 height-fit-content">{{ __('Save') }}</a>
    </div>
</div>
<table class="table notes-table table-borderless table-hover table-layout-fixed">
    <tbody>
        <tr class="{{ $order->packing_note == '' ? 'd-none' : '' }}">
            <td class="align-middle text-word-break" style="width: 85%;">
                <h4>{{ __('Note to pack') }}</h4>
                <textarea name="packing_note" class="form-control text-black">{{ $order->packing_note ?? '' }}</textarea>
                <div class="previewText">
                    {!! $order->packing_note ?? '' !!}
                </div>
            </td>
            <td class="align-middle">
                <div class="d-flex align-items-center mx-1 section-icons">
                    <div class="edit-tr-section mx-1">
                        <i class="picon-edit-filled icon-orange icon-lg" title="{{ __('Edit') }}" aria-hidden="true"></i>
                    </div>

                    <div class="save-section mx-1 d-none">
                        <i class="save picon-save-light icon-orange icon-lg" title="{{ __('Save') }}" aria-hidden="true"></i>
                        <i class="save-cancel picon-close-circled-light icon-orange icon-lg" title="{{ __('Cancel') }}" aria-hidden="true"></i>
                    </div>

                    <div class="d-flex align-items-center justify-content-center">
                        <svg class="d-none loading" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: rgb(255, 255, 255); display: block; shape-rendering: auto;" width="20px" height="20px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                            <circle cx="50" cy="50" fill="none" stroke="#f7860b" stroke-width="10" r="45" stroke-dasharray="164.93361431346415 56.97787143782138">
                                <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"/>
                            </circle>
                        </svg>
                        <div class="w4rAnimated_checkmark d-flex">
                            <i class="d-none saveSuccess picon-check-circled-light icon-orange icon-lg" title="{{ __('Save') }}" aria-hidden="true"></i>
                            <i class="d-none saveError picon-close-circled-light icon-red icon-lg" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <tr class="{{ $order->slip_note == '' ? 'd-none' : '' }}">
            <td class="align-middle text-word-break" style="width: 85%;">
                <h4>{{ __('Slip note') }}</h4>
                <textarea name="slip_note" class="form-control text-black">{{ $order->slip_note ?? '' }}</textarea>
                <div class="previewText">
                    {!! $order->slip_note ?? '' !!}
                </div>
            </td>
            <td class="align-middle">
                <div class="d-flex align-items-center mx-1 section-icons">
                    <div class="edit-tr-section mx-1">
                        <i class="picon-edit-filled icon-orange icon-lg" title="{{ __('Edit') }}" aria-hidden="true"></i>
                    </div>

                    <div class="save-section mx-1 d-none">
                        <i class="save picon-save-light icon-orange icon-lg" title="{{ __('Save') }}" aria-hidden="true"></i>
                        <i class="save-cancel picon-close-circled-light icon-orange icon-lg" title="{{ __('Cancel') }}" aria-hidden="true"></i>
                    </div>

                    <div class="d-flex align-items-center justify-content-center">
                        <svg class="d-none loading" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: rgb(255, 255, 255); display: block; shape-rendering: auto;" width="20px" height="20px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                            <circle cx="50" cy="50" fill="none" stroke="#f7860b" stroke-width="10" r="45" stroke-dasharray="164.93361431346415 56.97787143782138">
                                <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"/>
                            </circle>
                        </svg>
                        <div class="w4rAnimated_checkmark d-flex">
                            <i class="d-none saveSuccess picon-check-circled-light icon-orange icon-lg" title="{{ __('Save') }}" aria-hidden="true"></i>
                            <i class="d-none saveError picon-close-circled-light icon-red icon-lg" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <tr class="{{ $order->gift_note == '' ? 'd-none' : '' }}">
            <td class="align-middle text-word-break" style="width: 85%;">
                <h4>{{ __('Gift note') }}</h4>
                <textarea name="gift_note" class="form-control text-black">{{ $order->gift_note ?? '' }}</textarea>
                <div class="previewText">
                    {!! $order->gift_note ?? '' !!}
                </div>
            </td>
            <td class="align-middle">
                <div class="d-flex align-items-center mx-1 section-icons">
                    <div class="edit-tr-section mx-1">
                        <i class="picon-edit-filled icon-orange icon-lg" title="{{ __('Edit') }}" aria-hidden="true"></i>
                    </div>

                    <div class="save-section mx-1 d-none">
                        <i class="save picon-save-light icon-orange icon-lg" title="{{ __('Save') }}" aria-hidden="true"></i>
                        <i class="save-cancel picon-close-circled-light icon-orange icon-lg" title="{{ __('Cancel') }}" aria-hidden="true"></i>
                    </div>

                    <div class="d-flex align-items-center justify-content-center">
                        <svg class="d-none loading" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: rgb(255, 255, 255); display: block; shape-rendering: auto;" width="20px" height="20px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                            <circle cx="50" cy="50" fill="none" stroke="#f7860b" stroke-width="10" r="45" stroke-dasharray="164.93361431346415 56.97787143782138">
                                <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"/>
                            </circle>
                        </svg>
                        <div class="w4rAnimated_checkmark d-flex">
                            <i class="d-none saveSuccess picon-check-circled-light icon-orange icon-lg" title="{{ __('Save') }}" aria-hidden="true"></i>
                            <i class="d-none saveError picon-close-circled-light icon-red icon-lg" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <tr class="{{ $order->internal_note == '' ? 'd-none' : '' }}">
            <td class="align-middle text-word-break" style="width: 85%;">
                <h4>{{ __('Internal note') }}</h4>
                <textarea name="internal_note" class="form-control text-black">{{ $order->internal_note ?? '' }}</textarea>
                <div class="previewText">
                    {!! $order->internal_note ?? '' !!}
                </div>
            </td>
            <td class="align-middle">
                <div class="d-flex align-items-center mx-1 section-icons">
                    <div class="edit-tr-section mx-1">
                        <i class="picon-edit-filled icon-orange icon-lg" title="{{ __('Edit') }}" aria-hidden="true"></i>
                    </div>

                    <div class="save-section mx-1 d-none">
                        <i class="save picon-save-light icon-orange icon-lg" title="{{ __('Save') }}" aria-hidden="true"></i>
                        <i class="save-cancel picon-close-circled-light icon-orange icon-lg" title="{{ __('Cancel') }}" aria-hidden="true"></i>
                    </div>

                    <div class="d-flex align-items-center justify-content-center">
                        <svg class="d-none loading" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: rgb(255, 255, 255); display: block; shape-rendering: auto;" width="20px" height="20px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                            <circle cx="50" cy="50" fill="none" stroke="#f7860b" stroke-width="10" r="45" stroke-dasharray="164.93361431346415 56.97787143782138">
                                <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"/>
                            </circle>
                        </svg>
                        <div class="w4rAnimated_checkmark d-flex">
                            <i class="d-none saveSuccess picon-check-circled-light icon-orange icon-lg" title="{{ __('Save') }}" aria-hidden="true"></i>
                            <i class="d-none saveError picon-close-circled-light icon-red icon-lg" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </tbody>
</table>
