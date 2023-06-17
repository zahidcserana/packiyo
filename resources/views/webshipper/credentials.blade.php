<form method="post" action="{{ route('profile.update') }}" autocomplete="off"
      enctype="multipart/form-data">
    @csrf
    @method('put')

    <h6 class="heading-small text-muted mb-4">{{ __('User information') }}</h6>

    <x-toastr type="error" key="not_allow_profile" />

    <div class="pl-lg-4">
        <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-name">{{ __('Name') }}</label>
            <input type="text" name="name" id="input-name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" placeholder="{{ __('Name') }}" value="{{ old('name', auth()->user()->name) }}" required autofocus>
        </div>
        <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-email">{{ __('Email') }}</label>
            <input type="email" name="email" id="input-email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('Email') }}" value="{{ old('email', auth()->user()->email) }}" required>
        </div>
        <div class="form-group{{ $errors->has('photo') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-name">{{ __('Profile photo') }}</label>
            <div class="custom-file">
                <input type="file" name="photo" class="custom-file-input{{ $errors->has('photo') ? ' is-invalid' : '' }}" id="input-picture" accept="image/*">
                <label class="custom-file-label" for="input-picture">{{ __('Select profile photo') }}</label>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
        </div>
    </div>
</form>
