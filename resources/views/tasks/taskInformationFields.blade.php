<div class="form-group">
    <label class="form-control-label" for="input-role_id">{{__('Task Type')}}</label>
    <select name="task_type_id" id="input-role_id" class="form-control" data-toggle="select" data-placeholder="">
        @foreach($taskTypes as $taskType)
                <option value="{{$taskType->id}}" {{$task->task_type_id ?? '' === $taskType-> id ? 'selected' : ''}}>{{$taskType->name}}</option>
        @endforeach
    </select>
</div>
@include('shared.forms.input', [
   'name' => 'notes',
   'label' => __('Notes'),
   'value' => $task->notes ?? ''
])
@include('shared.forms.ajaxSelect', [
            'url' => route('task.filterUsers'),
            'name' => 'user_id',
            'className' => 'ajax-user-input',
            'placeholder' => 'Search',
            'label' => 'User search',
            'default' => [
                'id' => $task->user->id ?? old('user_id'),
                'text' => $task->user->contactInformation->name ?? ''
            ]
        ])
@include('shared.forms.ajaxSelect', [
            'url' => route('task.filterCustomers'),
            'name' => 'customer_id',
            'className' => 'ajax-user-input',
            'placeholder' => 'Search',
            'label' => 'Customer',
            'default' => [
                'id' => $task->customer->id ?? old('customer_id'),
                'text' => $task->customer->contactInformation->name ?? ''
            ]
        ])
