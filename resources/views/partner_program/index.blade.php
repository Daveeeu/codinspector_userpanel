@extends('layouts.master')
@section('title', __('partner_program_title'))
@section('content')
    <x-page-title title="{{ __('partner_program_title') }}" pagetitle="{{ __('partner_program_title') }}"/>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <h4 class="fw-bold">{{ __('partner_program_join_program') }}</h4>
                <p class="text-muted">{{ __('partner_program_earn_extra_income') }}</p>
            </div>

            @if($partnerRequest)
                <div class="status-container p-3 rounded-3 mb-3
                @if($partnerRequest->status == 'pending') bg-warning bg-opacity-10
                @elseif($partnerRequest->status == 'approved') bg-success bg-opacity-10
                @elseif($partnerRequest->status == 'rejected') bg-danger bg-opacity-10
                @endif">

                    @if($partnerRequest->status == 'pending')
                        <div class="d-flex align-items-center">
                            <i class="bi bi-hourglass-split fs-4 me-2 text-warning"></i>
                            <p class="mb-0">{{ __('partner_program_pending_status') }}</p>
                        </div>
                    @elseif($partnerRequest->status == 'approved')
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill fs-4 me-2 text-success"></i>
                            <div>
                                <p class="mb-0 fw-bold">{{ __('partner_program_approved_status') }}</p>
                                <p class="mb-0 mt-2">
                                    <span class="badge bg-success me-2">{{ $partnerRequest->commission_rate }}% {{ __('partner_program_approved_commission_rate') }}</span>
                                    <span class="badge bg-info">{{ $partnerRequest->validity_days }} {{ __('partner_program_approved_validity_days') }}</span>
                                </p>
                            </div>

                        </div>
                        <div class="row mt-4 g-3">
                            <div class="col-md-4">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-code-slash text-primary fs-3 mb-2"></i>
                                        <h6 class="card-title">{{ __('partner_program_partner_code') }}</h6>
                                        <p class="card-text fw-bold mb-0">REF123456</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-people text-primary fs-3 mb-2"></i>
                                        <h6 class="card-title">{{ __('partner_program_registered_users') }}</h6>
                                        <p class="card-text fw-bold mb-0">42 fő</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-currency-dollar text-primary fs-3 mb-2"></i>
                                        <h6 class="card-title">{{ __('partner_program_total_commission') }}</h6>
                                        <p class="card-text fw-bold mb-0">125.000 Ft</p>
                                    </div>
                                </div>
                            </div>
                            @elseif($partnerRequest->status == 'rejected')
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-x-circle-fill fs-4 me-2 text-danger"></i>
                                    <p class="mb-0">{{ __('partner_program_rejected_status') }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="benefits-section mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-currency-dollar fs-4 me-2 text-primary"></i>
                                        <span>{{ __('partner_program_attractive_commissions') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-graph-up-arrow fs-4 me-2 text-primary"></i>
                                        <span>{{ __('partner_program_increasing_revenue') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('partner_program.store') }}" method="POST">
                            @csrf
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-person-plus-fill me-2"></i>{{ __('partner_program_join_button') }}
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
        </div>

@endsection
