<form method="post" action="{{ route('customer.user', [ 'customer' => $customer]) }}" autocomplete="off">
    <table class="table align-items-center table-flush datatable-basic">
        <thead class="thead-light">
        <tr>
            <th scope="col">{{ __('Name') }}</th>
            <th scope="col">{{ __('Email') }}</th>
            <th scope="col"></th>
        </tr>
        </thead>
            @csrf
            <tbody>
            @foreach ($allUsers as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <div class="custom-control custom-checkbox custom-checkbox-success">
                            <input
                                class="custom-control-input"
                                name="users[]"
                                value="{{ $user->id}}"
                                id="chk-{{$user}}"
                                type="checkbox"
                                {{ in_array($user->id, old('users', $customerUsersIds) ?? []) ? 'checked="checked"' : '' }}
                            >
                            <label class="custom-control-label" for="chk-{{$user}}"></label>
                        </div>
                    </td>
                </tr>
            @endforeach
    </table>
<div class="text-center">
    <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
</div>
</tbody>
</form>

@push('css')
    <link rel="stylesheet" href="{{ asset('argon') }}/vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('argon') }}/vendor/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('argon') }}/vendor/datatables.net-select-bs4/css/select.bootstrap4.min.css">
@endpush

@push('js')
    <script src="{{ asset('argon') }}/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/datatables.net-select/js/dataTables.select.min.js"></script>
@endpush
