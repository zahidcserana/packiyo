@extends('layouts.app')

@section('content')
    @component('layouts.headers.auth', [
        'title' => __('Profile'),
        'subtitle' => __('Edit Profile')
    ])
    @endcomponent
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

            </div>
            <div class="col-12 mb-3">
                <div class="nav-wrapper">
                    <ul class="nav nav-pills nav-fill flex-md-row nav-bg-gray" id="tabs-icons-text" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link mb-sm-3 mb-md-0 active" id="profile-user-information-tab" data-toggle="tab"
                               href="#profile-user-information-tab-content" role="tab" aria-controls="tabs-icons-text-1"
                               aria-selected="true">
                                {{ __('User Information') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mb-sm-3 mb-md-0" id="profile-change-password-tab" data-toggle="tab"
                               href="#profile-change-password-tab-content" role="tab"
                               aria-controls="tabs-icons-text-2" aria-selected="false">
                                {{ __('Change Password') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="col-12 mt-2 alert-container"></div>
                    <div class="tab-content text-black" id="myTabContent">
                        <div class="tab-pane fade show active p-3" id="profile-user-information-tab-content" role="tabpanel"
                             aria-labelledby="profile-user-information-tab">
                             @include('profile.userInformation')
                        </div>
                        <div class="tab-pane fade p-3" id="profile-change-password-tab-content" role="tabpanel"
                             aria-labelledby="profile-change-password-tab">
                             @include('profile.changePassword')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
