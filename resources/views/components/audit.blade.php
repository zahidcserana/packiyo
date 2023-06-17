<div class="table-responsive pb-2 padding-sm items-table has-scrollbar">
    <table id="audit-log" class="table align-items-center table-small-paddings table-small-th-paddings table-th-small-font table-td-small-font table-logs datatable-basic">
        <thead>
        <tr>
            <th class="border-top-0 text-neutral-text-gray font-weight-600 text-center">{{ __('Date') }}</th>
            <th class="border-top-0 text-neutral-text-gray font-weight-600 text-center">{{ __('User') }}</th>
            <th class="border-top-0 text-neutral-text-gray font-weight-600 text-center">{{ __('Object') }}</th>
            <th class="border-top-0 text-neutral-text-gray font-weight-600 text-center">{{ __('Event') }}</th>
            <th class="border-top-0 text-neutral-text-gray font-weight-600">{{ __('Note') }}</th>
        </tr>
        </thead>
        <tbody id="audit-log-data">
            @include('components._logs')
        </tbody>
    </table>
</div>
