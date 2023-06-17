<div
    @if (isset($class))
        class="{{ $class }}"
    @endif
    data-toggle="modal"
    data-target="#print-modal"
    data-submit-action="{{ $submitAction }}"
    data-pdf-url="{{ $pdfUrl }}"
    data-customer-printers-url="{{ $customerPrintersUrl }}"
>
    @if (isset($slot))
        {{ $slot }}
    @else
        <button class="table-icon-button">
            <i class="pr-2 picon-printer-light icon-lg align-middle"></i>
        </button>
    @endif
</div>
