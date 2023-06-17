@props([
    'containerClass' => 'col-lg-3 col-md-6 col-xs-12',
    'labelClass' => '',
    'inputClass' => '',
    'minimumInputLength' => 3,
    'value' => [],
])

<div class="form-group select-tags {{ $containerClass }}">
    <label for="select-tag" class="{{ $labelClass }}" data-id="tags">
        {{ __('Tags') }}
    </label>

    <input type="hidden" name="tags" value="">

    <select
        name="tags[]"
        class="{{ $inputClass }} custom-select"
        data-ajax--url="{{ route('tag.filterInputTags') }}"
        data-minimum-input-length="{{ $minimumInputLength }}"
        data-placeholder="{{ __('Tags') }}"
        multiple
    >
        @foreach ($value ?? [] as $tag)
            <option selected value="{{ $tag->name }}">{{ $tag->name }}</option>
        @endforeach
    </select>
</div>
