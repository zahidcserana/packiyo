@extends('layouts.app')

@section('content')
    @component('layouts.headers.auth')
    @endcomponent
    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Task Types') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('task_type.create') }}" class="btn btn-sm btn-primary">{{ __('Add task type') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive p-4">
                        <table class="table align-items-center col-12" id="task-type-table">
                            <thead class="thead-light"></thead>
                            <tbody style="cursor:pointer"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        new TaskType();
    </script>
@endpush


