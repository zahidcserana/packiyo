@include('shared.forms.input', [
   'name' => 'name',
   'label' => __('Name'),
   'value' => $webhook->name ?? ''
])
<div class="form-group">
    <label class="form-control-label">{{ __('Object Type') }}</label>
    <select name="object_type" class="form-control" data-toggle="select" data-placeholder="">
        @foreach($object_types as $key => $object_type)
            <option value="{{$object_type}}"
                {{$webhook->object_type ?? '' === $object_type ? 'selected' : ''}}
            >{{$key}}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label class="form-control-label">{{ __('Operation') }}</label>
    <select name="operation" class="form-control" data-toggle="select" data-placeholder="">
        @foreach(['Store', 'Update', 'Destroy'] as $operation)
            <option value="{{$operation}}"
                {{$webhook->operation ?? '' === $operation ? 'selected' : ''}}
            >{{ __($operation) }}</option>
        @endforeach
    </select>
</div>
@include('shared.forms.input', [
   'name' => 'url',
   'label' => __('Url'),
   'value' => $webhook->url ?? ''

])
