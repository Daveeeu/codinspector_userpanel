@extends('layouts.auth')

@section('title', __('verify_title'))

@section('content')

    <div class="mx-3 mx-lg-0">

        <div class="card my-5 col-xl-9 col-xxl-8 mx-auto rounded-4 overflow-hidden p-4">
            <div class="row g-4">
                <div class="col-lg-6 d-flex">
                    <div class="card-body">
                        <img src="{{ URL::asset('build/images/logo1.png') }}" class="mb-4" width="145" alt="">
                        <h4 class="fw-bold">{{ __('verify_heading') }}</h4>
                        <p class="mb-4">{{ __('verify_description') }}</p>

                        @if (session('resent'))
                            <div class="alert alert-success" role="alert">
                                {{ __('verify_success_message') }}
                            </div>
                        @endif

                        <p class="mb-4">{{ __('verify_no_email_message') }}</p>
                        <form class="d-grid gap-2" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">{{ __('verify_resend_button') }}</button>
                        </form>

                        <p class="mt-4 text-muted">{!! __('verify_already_verified_message') !!}</p>
                    </div>
                </div>

                <!-- Oldalsó kép -->
                <div class="col-lg-6 d-lg-flex d-none">
                    <div class="p-3 rounded-4 w-100 d-flex align-items-center justify-content-center bg-light">
                        <img src="{{ URL::asset('build/images/auth/verify-email.png') }}" class="img-fluid" alt="E-mail Cím Ellenőrzése">
                    </div>
                </div>
            </div><!--end row-->
        </div>

    </div>

@endsection
