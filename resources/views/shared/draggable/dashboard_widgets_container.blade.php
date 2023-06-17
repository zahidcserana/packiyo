<div class="grid-stack"></div>
@push('widget-js')
    <script>
        new Widget({{$showGeoWidget ?? false}})
    </script>
@endpush
