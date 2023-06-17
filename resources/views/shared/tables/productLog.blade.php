<table class="table  col-12 text-left no-footer">
    <thead>
    <tr>
        <th class="border-top-0 text-neutral-text-gray font-weight-600 font-xs">{{ __('Date') }}</th>
        <th class="border-top-0 text-neutral-text-gray font-weight-600 font-xs">{{ __('User') }}</th>
        <th class="border-top-0 text-neutral-text-gray font-weight-600 font-xs">{{ __('Note') }}</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="py-4 text-black font-weight-600 font-sm">{{ $product->created_at }}</td>
        <td class="py-4 text-neutral-text-gray font-weight-600 font-sm">{{ $product->customer->contactInformation->name }} </td>
        <td class="py-4 text-black font-weight-600 font-sm">{{ __('Product Created') }}</td>
    </tr>
    @if (! empty($product->revisionHistory) && count($product->revisionHistory))
        @foreach($product->revisionHistory as $history)
            <tr>
                <td class="py-4 text-black font-weight-600 font-sm">{{ $history->updated_at }}</td>
                <td class="py-4 text-neutral-text-gray font-weight-600 font-sm">{{ $history->userResponsible()->contactInformation->name ?? '' }} </td>
                <td class="py-4 text-black font-weight-600 font-sm">
                    @if($history->is_image)
                        @if($history->action === 'Added')
                            {!! __('Added Image') . ' <em>' . $history->newValue() . '</em>' . ' ' . __('to') . ' <em>' . $history->fieldName() . '</em>' !!}
                        @else
                            {!! __('Removed Image') . ' <em>' . $history->newValue() . '</em>' . ' ' . __('from') . ' <em>' . $history->fieldName() . '</em>' !!}
                        @endif
                    @elseif($history->fieldName() === 'deleted_at')
                        @if(empty($history->oldValue()) && empty($history->oldValue()))
                            {!! __('Product was deleted') !!}
                        @else
                            {!! __('Product was recovered') !!}
                        @endif

                    @elseif(empty($history->oldValue()) && empty($history->oldValue()))
                        {!! __('Added') . ' <em>' . $history->newValue() . '</em>' . ' ' . __('to') . ' <em>' . $history->fieldName() . '</em>' !!}
                    @elseif(empty($history->newValue()) && empty($history->newValue()))
                        {!! __('Removed') . ' <em>' . $history->oldValue() . '</em>' . ' ' . __('from') . ' ' . $history->fieldName() !!}
                    @else
                        {!! __('Changed') . ' <em>' .$history->fieldName() . '</em>' . ' ' . __('from') . ' <em>' . ($history->oldValue() ?? __('null')) . '</em>' . ' ' . __('to') . ' <em>' . $history->newValue() . '</em>'   !!}
                    @endif
                </td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
