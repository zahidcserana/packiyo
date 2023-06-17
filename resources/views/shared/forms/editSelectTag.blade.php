<div class="{{ $containerClass ?? '' }} select-tags">
    @if(!empty($label))<label for="{{ $selectId ?? '' }}" class="{{ $labelClass ?? '' }} text-neutral-text-gray font-weight-600 font-xs" data-id="tags">{{ $label }}</label>@endif
    <select name="{{ $name ?? 'tags[]' }}" id="{{ $selectId ?? '' }}" class="custom-select {{ $selectClass ?? '' }}"
        data-ajax--url="{{ route('tag.filterInputTags') }}"
        data-minimum-input-length="{{ $minimumInputLength }}"
        @if (!empty($fixRouteAfter)) data-fix-route-after="{{ $fixRouteAfter }}" @endif
        @if (!empty($dropdownParent)) data-dropdown-parent="{{ $dropdownParent }}" @endif
        @if (!empty($disabled)) disabled="disabled" @endif
        multiple="multiple"
    >
        @if (!empty($default))
            @foreach ($default as $tag)
                <option selected value="{{ $tag->name }}">{{ $tag->name }}</option>
            @endforeach
        @endif
    </select>
</div>
