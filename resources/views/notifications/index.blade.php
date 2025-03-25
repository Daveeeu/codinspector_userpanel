@extends('layouts.master')
@section('title', __('notifications_index_title'))
@section('content')
    <style>
        .text-center {
            text-align: center;
            vertical-align: middle;
        }

        .form-check {
            display: inline-block;
        }

    </style>
    <x-page-title title="{{ __('notifications_index_title') }}" pagetitle="{{ __('notifications_index_title') }}"/>
    <div class="container">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h2 class="mb-4">{{ __('notifications_index_notification_type') }}</h2>
                <form action="{{ route('notifications.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Értesítési Típusok Táblázat -->
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                            <tr>
                                <th>{{ __('notifications_index_notification_type') }}</th>
                                <th class="text-center">{{ __('notifications_index_email_label') }}</th>
                                <th class="text-center">{{ __('notifications_index_sms_label') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <strong>{{ __('notifications_index_quota_limit_approaching') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ __('notifications_index_quota_limit_approaching_description') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="notifications[quota_limit-email]" id="quota_limit_email"
                                            {{ $userNotifications->contains('quota_limit-email') ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="notifications[quota_limit-sms]" id="quota_limit_sms"
                                            {{ $userNotifications->contains('quota_limit-sms') ? 'checked' : '' }}>
                                    </div>
                                </td>
                            </tr>

                            <!-- Kvóta elfogyott -->
                            <tr>
                                <td>
                                    <strong>{{ __('notifications_index_quota_exhausted') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ __('notifications_index_quota_exhausted_description') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="notifications[quota_exhausted-email]" id="quota_exhausted_email"
                                            {{ $userNotifications->contains('quota_exhausted-email') ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="notifications[quota_exhausted-sms]" id="quota_exhausted_sms"
                                            {{ $userNotifications->contains('quota_exhausted-sms') ? 'checked' : '' }}>
                                    </div>
                                </td>
                            </tr>

                            <!-- Fizetés sikeres -->
                            <tr>
                                <td>
                                    <strong>{{ __('notifications_index_payment_success') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ __('notifications_index_payment_success_description') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="notifications[payment_success-email]" id="payment_success_email"
                                            {{ $userNotifications->contains('payment_success-email') ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="notifications[payment_success-sms]" id="payment_success_sms"
                                            {{ $userNotifications->contains('payment_success-sms') ? 'checked' : '' }}>
                                    </div>
                                </td>
                            </tr>

                            <!-- Fizetési hiba -->
                            <tr>
                                <td>
                                    <strong>{{ __('notifications_index_payment_failure') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ __('notifications_index_payment_failure_description') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="notifications[payment_failure-email]" id="payment_failure_email"
                                            {{ $userNotifications->contains('payment_failure-email') ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="notifications[payment_failure-sms]" id="payment_failure_sms"
                                            {{ $userNotifications->contains('payment_failure-sms') ? 'checked' : '' }}>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>{{ __('notifications_index_subscription_renewal') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ __('notifications_index_subscription_renewal_description') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="notifications[subscription_renewal-email]" id="subscription_renewal_email"
                                            {{ $userNotifications->contains('subscription_renewal-email') ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="notifications[subscription_renewal-sms]" id="subscription_renewal_sms"
                                            {{ $userNotifications->contains('subscription_renewal-sms') ? 'checked' : '' }}>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>{{ __('notifications_index_monthly_referral_report') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ __('notifications_index_monthly_referral_report_description') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="notifications[monthly_referral_report-email]" id="monthly_referral_report_email"
                                            {{ $userNotifications->contains('monthly_referral_report-email') ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="notifications[monthly_referral_report-sms]" id="monthly_referral_report_sms"
                                            {{ $userNotifications->contains('monthly_referral_report-sms') ? 'checked' : '' }}>
                                    </div>
                                </td>
                            </tr>

                            </tbody>
                        </table>

                        <p>{{ __('notifications_index_sms_sent_to') }} {{ auth()->user()->phone_number }}.</p>
                        <button type="submit" class="btn btn-primary">{{ __('notifications_index_save_settings_button') }}</button>
                    </div>

                </form>

            </div> <!-- card-body -->
        </div> <!-- card -->
    </div> <!-- container -->
@endsection
