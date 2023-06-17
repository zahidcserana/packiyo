<form method="post" action="{{ route('profile.update') }}" autocomplete="off"
      enctype="multipart/form-data">
    @csrf
    @method('put')

    <div class="form-group mb-0 mx-2 text-left mb-3">
        <span class="ml-2 avatar avatar-w-120 profile-img position-relative rounded-circle">
            <img id="preview-image" src="{{ auth()->user()->profilePicture() }}" class="{{ auth()->user()->picture == '' ? 'd-none' : '' }}" alt="Image placeholder" />
            <a href="#" id="preview-upload-profile-image" class="d-flex justify-content-center align-items-center upload-img-link position-absolute rounded-circle">
                <i class="picon-edit-filled icon-orange"></i>
            </a>
        </span>
        <div class="custom-file d-none">
            <input type="file" name="photo" class="custom-file-input" id="input-picture" accept="image/*">
        </div>
    </div>
    <div class="form-group mb-0 mx-2 text-left mb-3">
        <label for=""
               class="text-neutral-text-gray font-weight-600 font-xs"
               data-id="name">{{ __('Name') }} </label>
        <div
            class="input-group input-group-alternative input-group-merge tableSearch mw-100">
            <input
                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                value="{{ old('name', auth()->user()->contactInformation->name) }}"
                placeholder=""
                type="text"
                name="name" required>
        </div>
    </div>
    <div class="form-group mb-0 mx-2 text-left mb-3">
        <label for=""
               class="text-neutral-text-gray font-weight-600 font-xs"
               data-id="email">{{ __('Email') }}</label>
        <div
            class="input-group input-group-alternative input-group-merge tableSearch mw-100">
            <input
                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                value="{{ old('name', auth()->user()->email) }}"
                placeholder=""
                type="email"
                name="email" required>
        </div>
    </div>

    <button type="submit" class="btn bg-logoOrange mx-auto px-5 font-weight-700 mt-5 mb-2 change-tab text-white d-block" data-id="">
        {{ __('Save') }}
    </button>
</form>

@push('js')
    <script>
        ProfileForm();
    </script>
@endpush
