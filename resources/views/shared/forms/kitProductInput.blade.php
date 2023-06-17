<div class="border-bottom-gray mb-3 headingSection {{ ! $visible ? 'd-none' : '' }}">
    <h6 class="heading-small text-muted text-left mb-2">{{ __('Kit Items') }}</h6>
</div>
<div
    class = "form-group w-100 {{ ! $visible ? 'd-none' : '' }}"
    id = "rows_container"
    data-url = "{{ $url }}"
    data-className = "{{ $className }}"
    data-placeholder = "{{ $placeholder }}"
    data-label = "{{ $label1 }}"
>
    <div class="validation_errors text-danger text-left text-xs"></div>
    <table class="w-100 text-left" id="kit-items-table">
        <tr>
            <td class="form-control-label py-2">{{ $label1 }}</td>
            <td class="form-control-label py-2">{{ $label2 }}</td>
        </tr>
        @if(! empty($defaults) && count($defaults))
            @foreach($defaults as $default)
                <tr data-index="{{ $loop->index }}">
                    <td>
                        <div class="input-group input-group-alternative input-group-merge text-left">
                            <select
                                name="kit_items[{{$loop->index}}][id]"
                                class="{{ $className }}"
                                data-ajax--url="{{ $url }}"
                                data-placeholder="{{ $placeholder }}"
                                data-minimum-input-length="1"
                                data-toggle="select" >
                                <option selected value="{{ $default->id }}">{{ $default->name }}</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-alternative input-group-merge">
                            <input type="number" name="kit_items[{{ $loop->index }}][quantity]" value="{{ $default->pivot->quantity }}" class="form-control font-sm bg-white font-weight-600 text-neutral-gray h-auto p-2">
                        </div>
                    </td>
                    <td class="delete-row"><div><i class="fas fa-trash-alt text-lightGrey"></i></div></td>
                </tr>
            @endforeach
        @else
            <tr data-index="0">
                <td>
                    <div class="input-group input-group-alternative input-group-merge text-left">
                        <select
                            name="kit_items[0][id]"
                            class="{{ $className }}"
                            data-ajax--url="{{ $url }}"
                            data-placeholder="{{ $placeholder }}"
                            data-minimum-input-length="1"
                            data-toggle="select" >
                        </select>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-alternative input-group-merge">
                        <input type="number" name="kit_items[0][quantity]" value="1" class="form-control font-sm bg-white font-weight-600 text-neutral-gray h-auto p-2">
                    </div>
                </td>
                <td class="delete-row"><div><i class="fas fa-trash-alt text-lightGrey"></i></div></td>
            </tr>
        @endif
    </table>
    <div class="d-flex justify-content-center">
        <button type="button" class="btn bg-logoOrange text-white w-100 my-4 font-weight-700 fa fa-plus mt-3 w-100" id="add_row"></button>
    </div>
</div>
<script>
    window.excludedIds = @json($defaults ? $defaults->pluck('id') : []);
</script>
