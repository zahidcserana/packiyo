<div class="modal fade confirm-dialog" id="upgrade-enterprise">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="get" action="{{ route('account.upgrade', ['customer' => $customer]) }}" autocomplete="off">
                @csrf
                <div class="modal-header px-0 d-flex flex-column align-items-center">
                    <h2 class="text-black font-lg font-weight-600 modal-title">
                        {{ __('Upgrade to Enterprise Subscription') }}
                    </h2>
                </div>
                <div class="modal-body overflow-auto py-0">
                    <div class="text-neutral-text-gray">
                        <div>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                            Maecenas fermentum, lacus id eleifend porttitor, velit justo mollis lorem, nec iaculis turpis lectus id metus.
                            Nulla facilisi. Cras tincidunt faucibus metus nec lacinia. Nunc quis pharetra nulla. Nam nec faucibus justo, a bibendum turpis.
                            Morbi quis ullamcorper dolor. In molestie congue efficitur.
                        </div>
                    </div>
                </div>
                <div class="modal-footer mx-auto">
                    <button type="button" class="btn btn-secondary modal-button-cancel" data-dismiss="modal">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-secondary modal-button-cancel bg-logoOrange text-white" id="upgrade-subscription">
                        {{ __('Upgrade') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

