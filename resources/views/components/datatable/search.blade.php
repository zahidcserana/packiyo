@props([
    'search' => true,
    'searchClass' => '',
    'searchPlaceholder' => '',
])

@if ($search)
    <div class="form-group mb-0">
        <div class="input-group input-group-alternative input-group-merge bg-lightGrey font-sm tableSearch">
            <div class="input-group-prepend">
                <span class="input-group-text bg-lightGrey">
                    <img src="{{ asset('img/search.svg') }}" alt="">
                </span>
            </div>
            
            <input
                class="form-control font-sm bg-lightGrey font-weight-600 text-neutral-gray searchText px-2 py-0 {{ $searchClass }}"
                placeholder="{{ $searchPlaceholder }}"
                type="text"
            >
        </div>
    </div>
@endif