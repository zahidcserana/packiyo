<div class="nav-item dropdown">
    @if (app()->user->getCustomers()->count() > 1)
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{ isset($sessionCustomer) ? $sessionCustomer->contactInformation->name : __('All') }}
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="overflow: scroll;max-height: 60vh">
            <a class="dropdown-item" href="{{ route('user.removeSessionCustomer') }}" >All</a>
            @foreach (app()->user->getCustomers() as $customer)
                <a class="dropdown-item" href="{{ route('user.setSessionCustomer', ['customer' => $customer->id])}}">{{ $customer->contactInformation->name ?? '' }}</a>
            @endforeach
        </div>
    @endif
</div>

