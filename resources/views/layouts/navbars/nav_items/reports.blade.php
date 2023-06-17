@php
    $reportMenuReportPageViewNames = (!empty($page) &&
        (
            in_array($page, [
                'report.view'
            ])
        )
    );

    $submenuItems = [
        'shipment' => __('Shipments'),
        'shipped_item' => __('Shipped Items'),
        'picker' => __('Pickers'),
        'packer' => __('Packers'),
        'replenishment' => __('Replenishment'),
        'stale_inventory' => __('Stale Inventory'),
        'serial_number' => __('Serial Numbers'),
        'picking_batch' => __('Picking Batch')
    ];
@endphp
<li class="nav-item">
    <a class="nav-link" href="#reports" data-toggle="collapse" role="button" aria-expanded="{{ $reportMenuReportPageViewNames ? 'true' : 'false' }}" aria-controls="reports">
        <i class="picon-growth-light"></i>
        <span class="nav-link-text">{{ __('Reports') }}</span>
    </a>
    <div class="collapse {{ $reportMenuReportPageViewNames ? 'show' : '' }}" id="reports">
        <ul class="nav nav-sm flex-column">
            @foreach($submenuItems as $reportId => $reportTitle)
                @if (menu_item_visible($reportId))
                <li class="nav-item collapse-line">
                    <a href="{{ route('report.view', ['reportId' => $reportId]) }}" class="nav-link"><p class="{{ $reportMenuReportPageViewNames && request()->reportId == $reportId ? 'active_item' : '' }}">{{$reportTitle }}</p></a>
                </li>
                @endif
            @endforeach
        </ul>
    </div>
</li>

