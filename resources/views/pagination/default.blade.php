@if ($paginator->hasPages())
    <ul class="pagination d-flex align-items-center justify-content-center">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="previous disabled"><span><i class="picon-arrow-backward-filled icon-lg"></i></span></li>
        @else
            <li class="previous" title="{{ __('Previous') }}"><a href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="picon-arrow-backward-filled icon-lg"></i></a></li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="disabled"><span>{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page active"><span>{{ $page }}</span></li>
                    @else
                        <li class="page"><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="next" title="{{ __('Next') }}"><a href="{{ $paginator->nextPageUrl() }}" rel="next"><i class="picon-arrow-forward-filled icon-lg"></i></a></li>
        @else
            <li class="next disabled"><span><i class="picon-arrow-forward-filled icon-lg"></i></span></li>
        @endif
    </ul>
    <div class="font-sm">{{__('Showing')}} {{($paginator->currentpage()-1)*$paginator->perpage()+1}} {{ __('to') }} {{$paginator->currentpage()*$paginator->perpage()}}
        {{ __('of') }}  {{$paginator->total()}} {{ __('entries') }}
    </div>
@endif
