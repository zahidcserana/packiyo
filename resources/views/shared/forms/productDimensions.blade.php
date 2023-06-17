<div class="form-group{{ $errors->has('width') ? ' has-danger' : '' }}">
    <label class="form-control-label" for="input-width">
        Width in
            <span class="dimensions-label">
                {{ \App\Models\Customer::DIMENSION_UNITS[isset($product) ? $product->customer->dimensions_unit : 'cm'] }}
            </span>
    </label>
    <input type="text" name="width" id="input-width" class="form-control{{ $errors->has('width') ? ' is-invalid' : '' }}" placeholder="Width" value="{{ old(dot('width'), $product->width ?? '') }}">
</div>
<div class="form-group{{ $errors->has('length') ? ' has-danger' : '' }}">
    <label class="form-control-label" for="input-length">
        Length in
        <span class="dimensions-label">
                {{ \App\Models\Customer::DIMENSION_UNITS[isset($product) ? $product->customer->dimensions_unit : 'cm'] }}
            </span>
    </label>
    <input type="text" name="length" id="input-length" class="form-control{{ $errors->has('width') ? ' is-invalid' : '' }}" placeholder="Length" value="{{ old(dot('length'), $product->length ?? '') }}">
</div>
<div class="form-group{{ $errors->has('height') ? ' has-danger' : '' }}">
    <label class="form-control-label" for="input-height">
        Height in
        <span class="dimensions-label">
                {{ \App\Models\Customer::DIMENSION_UNITS[isset($product) ? $product->customer->dimensions_unit : 'cm'] }}
            </span>
    </label>
    <input type="text" name="height" id="input-height" class="form-control{{ $errors->has('height') ? ' is-invalid' : '' }}" placeholder="Height" value="{{ old(dot('height'), $product->height ?? '') }}">
</div>
<div class="form-group{{ $errors->has('weight') ? ' has-danger' : '' }}">
    <label class="form-control-label" for="input-weight">
        Weight in
        <span class="weight-label">
                {{ \App\Models\Customer::WEIGHT_UNITS[isset($product) ? $product->customer->weight_unit : 'kg'] }}
            </span>
    </label>
    <input type="text" name="weight" id="input-weight" class="form-control{{ $errors->has('weight') ? ' is-invalid' : '' }}" placeholder="Weight" value="{{ old(dot('weight'), $product->weight ?? '') }}">
</div>
