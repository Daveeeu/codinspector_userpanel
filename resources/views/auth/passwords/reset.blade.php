@extends('layouts.auth')

@section('title', __('reset_title'))

@section('content')
    <div class="mx-3 mx-lg-0">
        <div class="card my-5 col-xl-9 col-xxl-8 mx-auto rounded-4 overflow-hidden p-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6 d-flex">
                    <div class="card-body">
                        <img src="/logo.svg" class="mb-4" width="145" alt="Logo">
                        <h4 class="fw-bold">{{ __('reset_heading') }}</h4>
                        <p class="mb-0">{{ __('reset_description') }}</p>

                        <div class="form-body mt-4">
                            <form class="row g-3" method="POST" action="{{ route('password.update') }}">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">

                                <!-- E-mail mező -->
                                <div class="col-12">
                                    <label class="form-label" for="email">{{ __('reset_email_label') }}</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           name="email" value="{{ $email ?? old('email') }}" id="email" placeholder="{{ __('reset_email_placeholder') }}">
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>

                                <!-- Új jelszó mező -->
                                <div class="col-12">
                                    <label class="form-label" for="password">{{ __('reset_new_password_label') }}</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           name="password" id="password" placeholder="{{ __('reset_new_password_placeholder') }}">
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>

                                <!-- Jelszó megerősítése mező -->
                                <div class="col-12">
                                    <label class="form-label" for="password_confirmation">{{ __('reset_confirm_password_label') }}</label>
                                    <input type="password" class="form-control"
                                           name="password_confirmation" id="password_confirmation" placeholder="{{ __('reset_confirm_password_placeholder') }}">
                                </div>

                                <!-- Gombok -->
                                <div class="col-12">
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">{{ __('reset_change_password_button') }}</button>
                                        <a href="{{ route('login') }}" class="btn btn-light">{{ __('reset_back_to_login_button') }}</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Oldalsó kép -->
                <div class="col-lg-6 d-lg-flex d-none">
                    <div class="p-3 rounded-4 w-100 d-flex align-items-center justify-content-center bg-light">
                        <img src="{{ URL::asset('build/images/auth/reset-password1.png') }}" class="img-fluid" alt="{{ __('reset_title') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
