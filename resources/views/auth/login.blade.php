@extends('layouts.auth')

@section('title', __('login_title'))

@section('content')

    <div class="mx-3 mx-lg-0">

        <div class="card my-5 col-xl-9 col-xxl-8 mx-auto rounded-4 overflow-hidden p-4">
            <div class="row g-4">
                <div class="col-lg-6 d-flex">
                    <div class="card-body">
                        <img src="logo.svg" class="mb-4" width="145" alt="">
                        <h4 class="fw-bold">{{ __('login_heading') }}</h4>
                        <p class="mb-0">{{ __('login_description') }}</p>

                        <div class="form-body mt-4">
                            <form class="row g-3" method="POST" action="{{ route('login') }}">
                                @csrf
                                <!-- E-mail mező -->
                                <div class="col-12">
                                    <label for="inputEmailAddress" class="form-label">{{ __('login_email_label') }}</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="inputEmailAddress" name="email" value="{{ old('email') }}" placeholder="{{ __('login_email_placeholder') }}">
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                                    @enderror
                                </div>

                                <!-- Jelszó mező -->
                                <div class="col-12">
                                    <label for="inputChoosePassword" class="form-label">{{ __('login_password_label') }}</label>
                                    <div class="input-group" id="show_hide_password">
                                        <input type="password" class="form-control border-end-0 @error('password') is-invalid @enderror" id="inputChoosePassword" name="password"
                                               placeholder="{{ __('login_password_placeholder') }}">
                                        <a href="javascript:;" class="input-group-text bg-transparent"><i
                                                class="bi bi-eye-slash-fill"></i></a>
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Emlékezz rám -->
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="flexSwitchCheckChecked">{{ __('login_remember_me') }}</label>
                                    </div>
                                </div>

                                <!-- Elfelejtett jelszó -->
                                <div class="col-md-6 text-end">
                                    <a href="{{ route('password.request') }}">{{ __('login_forgot_password') }}</a>
                                </div>

                                <!-- Bejelentkezés gomb -->
                                <div class="col-12">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">{{ __('login_button') }}</button>
                                    </div>
                                </div>

                                <!-- Regisztráció link -->
                                <div class="col-12">
                                    <div class="text-start">
                                        <p class="mb-0">{{ __('login_no_account') }}
                                            <a href="{{ route('register') }}">{{ __('login_sign_up_here') }}</a>
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Oldalsó kép -->
                <div class="col-lg-6 d-lg-flex d-none">
                    <div class="p-3 rounded-4 w-100 d-flex align-items-center justify-content-center bg-light">
                        <img src="{{ URL::asset('build/images/auth/login1.png') }}" class="img-fluid" alt="">
                    </div>
                </div>

            </div><!--end row-->
        </div>

    </div>

@endsection
