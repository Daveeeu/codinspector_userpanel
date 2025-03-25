@extends('layouts.master')

@section('title', __('account_settings'))

@section('content')
    <div class="card col-xl-9 col-xxl-8 mx-auto rounded-4 overflow-hidden p-4">
        <h4 class="fw-bold">{{ __('account_settings') }}</h4>
        <p class="mb-3">{{ __('edit_personal_info') }}</p>

        <div class="form-body">
            <form method="POST" action="{{ route('account.update') }}">
                @csrf
                @method('PUT')

                <!-- Név mezők -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">{{ __('first_name') }}</label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                               name="first_name" id="first_name" value="{{ old('first_name', Auth::user()->first_name) }}" placeholder="{{ __('first_name_placeholder') }}">
                        @error('first_name')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="last_name" class="form-label">{{ __('last_name') }}</label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                               name="last_name" id="last_name" value="{{ old('last_name', Auth::user()->last_name) }}" placeholder="{{ __('last_name_placeholder') }}">
                        @error('last_name')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <!-- E-mail mező -->
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('email') }}</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           name="email" id="email" value="{{ old('email', Auth::user()->email) }}" placeholder="{{ __('email_placeholder') }}">
                    @error('email')
                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <!-- Telefonszám mező -->
                <div class="mb-3">
                    <label for="phone" class="form-label">{{ __('phone_number') }}</label>
                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                           name="phone_number" id="phone" value="{{ old('phone_number', Auth::user()->phone_number) }}" placeholder="{{ __('phone_number_placeholder') }}">
                    @error('phone_number')
                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <!-- Jelszó mezők -->
                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('new_password') }}</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           name="password" id="password" placeholder="{{ __('new_password_placeholder') }}">
                    @error('password')
                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">{{ __('password_confirmation') }}</label>
                    <input type="password" class="form-control"
                           name="password_confirmation" id="password_confirmation" placeholder="{{ __('password_confirmation_placeholder') }}">
                </div>

                <!-- 2FA -->
                <div class="mb-3">
                    <label class="form-label">{{ __('two_factor_authentication') }}</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="2fa" id="2fa"
                            {{ Auth::user()->two_factor_enabled ? 'checked' : '' }}>
                        <label class="form-check-label" for="2fa">{{ __('enable_2fa') }}</label>
                    </div>
                </div>

                <!-- Adatok mentése gomb -->
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">{{ __('save_changes') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card col-xl-9 col-xxl-8 mx-auto rounded-4 overflow-hidden p-4">
        <div class="account-deletion">
            <h5 class="text-danger fw-bold">{{ __('delete_account_title') }}</h5>
            <p class="text-muted">{{ __('delete_account_warning') }}</p>
            <form id="delete-account-form" method="POST" action="{{ route('account.delete') }}">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-outline-danger" id="delete-account-btn">
                    {{ __('delete_account_button') }}
                </button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('delete-account-btn').addEventListener('click', function () {
            Swal.fire({
                title: '{{ __("delete_account_confirm_title") }}',
                text: "{{ __('delete_account_confirm_text') }}",
                icon: 'warning',
                theme: 'auto',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __("delete_account_confirm_yes") }}',
                cancelButtonText: '{{ __("delete_account_confirm_no") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-account-form').submit();
                }
            });
        });
    </script>
@endsection
