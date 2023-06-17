@props([
    'type' => 'success',
    'key' => null,
])

@if (Session::has($key ?? 'status'))
    @php
        $notification = Session::get($key ?? 'status');

        if (is_array($notification)) {
            $type = $notification['type'];
            $notification = $notification['message'];
        }
    @endphp

    <script>
        @switch($type)
            @case('info')
            @case('error')
            @case('warning')
                toastr.{{ $type }}('{{ $notification }}')
                @break

            @default
                toastr.success('{{ $notification }}')
                @break
        @endswitch
    </script>
@endif

@if (! $errors->isEmpty())
    <script>
        @foreach ($errors->all() as $error)
            toastr.error('{{ $error }}')
        @endforeach
    </script>
@endif
