<div class="modal fade" id="export-{{ $reportId }}-modal" role="dialog">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content bg-white">
            <form method="post" action="{{ route('report.export', ['reportId' => $reportId]) }}" autocomplete="off"
                  class="export-form modal-content" id="export-{{ $reportId }}-report-form">
                @csrf
                <div class="modal-header border-bottom mx-4 px-0">
                    <h6 class="modal-title text-black text-left"
                        id="modal-title-notification">{{ __('Export :reportTitle', ['reportTitle' => $reportTitle]) }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true" class="text-black">&times;</span>
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="submit"
                            class="btn bg-logoOrange mx-auto px-5 text-white">{{ __('Export') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
