<div class="modal fade confirm-dialog" id="print-pdf" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header px-0">
                <div class="mx-4 pb-4 d-flex w-100">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true" class="text-black">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body text-white text-center overflow-auto pt-0">
                <p class="text-black font-md font-weight-600 px-4">
                    {{ __('Generated PDF') }}
                </p>
                <div class="d-flex flex-wrap justify-content-center pdf-url py-4">
                    <a href="" target="_blank" class="btn bg-logoOrange mx-auto px-lg-5 text-white float-right">
                        {{ __('Open PDF') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
