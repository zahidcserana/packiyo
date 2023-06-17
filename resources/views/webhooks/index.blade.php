<hr class="my-4" />
<div class="pl-lg-4">
    <div class="row align-items-center">
        <div class="col-12 text-right">
            <a href="{{ route('webhook.create') }}" class="btn btn-sm btn-primary">{{ __('Add webhook') }}</a>
        </div>
    </div>
    <div class="table-responsive py-4">
        <table class="table align-items-center table-flush datatable-basic">
            <thead class="thead-light">
            <tr>
                <th scope="col">{{ __('Name') }}</th>
                <th scope="col">{{ __('Operation') }}</th>
                <th scope="col">{{ __('Url') }}</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($webhooks as $webhook)
                <tr>
                    <td>{{ $webhook->name }}</td>
                    <td>{{ $webhook->operation }}</td>
                    <td>{{ $webhook->url }}</td>
                    <td class="text-right">
                        <a href="{{ route('webhook.edit', [ 'webhook' => $webhook ]) }}" class="btn btn-primary">{{ __('Edit') }}</a>
                        <form action="{{ route('webhook.destroy', ['webhook' => $webhook, 'id' => $webhook->id]) }}" method="post" style="display: inline-block">
                            @csrf
                            @method('delete')
                            <button type="button" class="btn btn-danger" data-confirm-message="{{ __('Are you sure you want to delete this webhook?') }}">
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>

<x-toastr key="webhook_status" />
