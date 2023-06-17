    <h6 class="heading-small text-muted mb-4">{{ __('Access Tokens') }}</h6>

    <x-toastr key="access_token_status" />
    <x-toastr type="error" key="not_allow_password" />

    <table class="table">
        <thead>
        <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Creation date') }}</th>
            <th>{{ __('Last used date') }}</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
            @foreach (auth()->user()->tokens as $token)
                <tr>
                    <td>{{ $token->name }}</td>
                    <td>{{ $token->created_at }}</td>
                    <td>{{ $token->last_used_at ?? __('Never') }}</td>
                    <td>
                        <form action="{{ route('profile.delete_access_token', ['token' => $token]) }}" method="post" style="display: inline-block">
                            @csrf
                            @method('delete')
                            <button class="btn btn-outline-danger" type="submit" data-confirm-message="{{ __('Are you sure you want to delete this token?') }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h6 class="heading-small text-muted my-4">{{ __('Create new token') }}</h6>

    <form method="post" action="{{ route('profile.create_access_token') }}">
        @csrf
        <div class="form-group">
            <label class="form-control-label" for="access_token_name">{{ __('Token name') }}</label>
            <input type="text" class="form-control" name="name" id="access_token_name" />
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
        </div>
    </form>
