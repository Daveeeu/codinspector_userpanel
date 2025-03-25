@extends('layouts.auth')

@section('title', 'Vertical')
@section('content')

    <body class="bg-error">

    <!-- Start wrapper-->
    <div class="pt-5">

        <div class="container pt-5">
            <div class="row pt-5">
                <div class="col-lg-12">
                    <div class="text-center error-pages">
                        <h1 class="error-title text-danger mb-3">403</h1>
                        <h2 class="error-sub-title text-white">Forbidden error</h2>

                        <p class="error-message text-white text-uppercase">You don't have permission to do this</p>

                        <div class="mt-4 d-flex align-items-center justify-content-center gap-3">
                            <a href="{{ url('/') }}" class="btn btn-outline-light rounded-5 px-4"><i class="bi bi-house-fill me-2"></i>Go To Home</a>
                        </div>

                        <div class="mt-4">
                            <p class="text-light">Copyright © {{ date('Y') }} | All rights reserved.</p>
                        </div>
                    </div>
                </div>
            </div><!--end row-->
        </div>

    </div><!--wrapper-->
@endsection
