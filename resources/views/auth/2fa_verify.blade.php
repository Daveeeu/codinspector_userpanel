@extends('layouts.auth')

@section('title', __('2fa_title'))

@section('content')
    <div class="mx-3 mx-lg-0">
        <div class="card my-5 col-xl-9 col-xxl-8 mx-auto rounded-4 overflow-hidden p-4">
            <div class="card-body">
                <h4 class="fw-bold">{{ __('2fa_heading') }}</h4>
                <p>{{ __('2fa_description') }}</p>

                @if ($errors->has('two_factor_code'))
                    <div class="alert alert-danger">{{ __('2fa_error_message') }}</div>
                @endif

                <form method="POST" action="{{ route('2fa.verify') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="two_factor_code" class="form-label">{{ __('2fa_code_label') }}</label>
                        <input type="text" name="two_factor_code" id="two_factor_code" class="form-control" placeholder="{{ __('2fa_code_placeholder') }}" required autofocus>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">{{ __('2fa_verify_button') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
