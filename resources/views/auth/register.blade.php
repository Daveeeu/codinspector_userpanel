@extends('layouts.auth')

@section('title', __('register_title'))

@section('content')

    <div class="mx-3 mx-lg-0">
        <div class="card my-5 col-xl-9 col-xxl-8 mx-auto rounded-4 overflow-hidden border-3 p-4">
            <div class="row g-4">
                <div class="col-lg-6 d-flex">
                    <div class="card-body">
                        <img src="{{ URL::asset('build/images/logo1.png') }}" class="mb-4" width="145" alt="">
                        <h4 class="fw-bold">{{ __('register_heading') }}</h4>
                        <p class="mb-0">{{ __('register_description') }}</p>
                        <div class="form-body mt-4">
                            <form class="row g-3" method="POST" action="{{ route('register') }}">
                                @csrf

                                <!-- Keresztnév -->
                                <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">{{ __('register_first_name_label') }}</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                           name="first_name" value="{{ old('first_name') }}"
                                           id="inputFirstName" placeholder="{{ __('register_first_name_placeholder') }}">
                                    @error('first_name')
                                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
                                    @enderror
                                </div>

                                <!-- Vezetéknév -->
                                <div class="col-md-6">
                                    <label for="inputLastName" class="form-label">{{ __('register_last_name_label') }}</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                           name="last_name" value="{{ old('last_name') }}"
                                           id="inputLastName" placeholder="{{ __('register_last_name_placeholder') }}">
                                    @error('last_name')
                                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
                                    @enderror
                                </div>

                                <!-- E-mail cím -->
                                <div class="col-12">
                                    <label for="inputEmailAddress" class="form-label">{{ __('register_email_label') }}</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" id="inputEmailAddress" placeholder="{{ __('register_email_placeholder') }}">
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                                    @enderror
                                </div>

                                <!-- Telefonszám -->
                                <div class="col-12">
                                    <label for="inputPhoneNumber" class="form-label">{{ __('register_phone_label') }}</label>
                                    <input type="number" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number') }}" id="inputPhoneNumber" placeholder="{{ __('register_phone_placeholder') }}">
                                    @error('phone_number')
                                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                                    @enderror
                                </div>

                                <!-- Jelszó -->
                                <div class="col-12">
                                    <label for="inputChoosePassword" class="form-label">{{ __('register_password_label') }}</label>
                                    <div class="input-group" id="show_hide_password">
                                        <input type="password" class="form-control border-end-0 @error('password') is-invalid @enderror" name="password" id="inputChoosePassword" placeholder="{{ __('register_password_placeholder') }}">
                                        <a href="javascript:;" class="input-group-text bg-transparent"><i class="bi bi-eye-slash-fill"></i></a>
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Jelszó megerősítése -->
                                <div class="col-12">
                                    <label for="inputChoosePassword" class="form-label">{{ __('register_confirm_password_label') }}</label>
                                    <div class="input-group" id="show_hide_password">
                                        <input type="password" class="form-control border-end-0" name="password_confirmation" id="password_confirmation" placeholder="{{ __('register_confirm_password_placeholder') }}">
                                        <a href="javascript:;" class="input-group-text bg-transparent"><i class="bi bi-eye-slash-fill"></i></a>
                                    </div>
                                </div>

                                <!-- Adatvédelmi szabályzat -->
                                <div class="form-check mt-3">
                                    <input type="checkbox" name="accepted_privacy_policy"
                                           class="form-check-input @error('accepted_privacy_policy') is-invalid @enderror"
                                           id="privacy_policy">
                                    <label class="form-check-label" for="privacy_policy">
                                        {{ __('register_privacy_policy_text') }} <a href="">Adatvédelmi Szabályzat</a>
                                    </label>
                                    @error('accepted_privacy_policy')
                                    <span class="invalid-feedback">
                      <strong>{{ $message }}</strong>
                  </span>
                                    @enderror
                                </div>

                                <!-- ÁSZF -->
                                <div class="form-check mt-3">
                                    <input type="checkbox" name="accepted_terms_of_service"
                                           class="form-check-input @error('accepted_terms_of_service') is-invalid @enderror"
                                           id="terms_of_service">
                                    <label class="form-check-label" for="terms_of_service">
                                        {{ __('register_terms_of_service_text') }} <a href="">ÁSZF</a>
                                    </label>
                                    @error('accepted_terms_of_service')
                                    <span class="invalid-feedback">
                      <strong>{{ $message }}</strong>
                  </span>
                                    @enderror
                                </div>

                                <!-- Regisztráció gomb -->
                                <div class="col-12">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">{{ __('register_button') }}</button>
                                    </div>
                                </div>

                                <!-- Már van fiókod? -->
                                <div class="col-12">
                                    <div class="text-start">
                                        <p class="mb-0">{{ __('register_already_have_account') }} <a href="{{ route('login') }}">{{ __('register_sign_in_here') }}</a></p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Oldalsó kép -->
                <div class="col-lg-6 d-lg-flex d-none">
                    <div class="p-3 rounded-4 w-100 d-flex align-items-center justify-content-center bg-light">
                        <img src="{{ URL::asset('build/images/auth/register1.png') }}" class="img-fluid" alt="">
                    </div>
                </div>
            </div><!--end row-->
        </div>
    </div>

@endsection
