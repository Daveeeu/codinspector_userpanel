<!doctype html>
<html lang="en" data-bs-theme="{{ $theme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="{{ asset('icon.ico') }}" type="image/png">
    <title>@yield('title') | Inspector Ramburs </title>

    @yield('css')

    @include('layouts.head-css')
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>

<body>

@include('layouts.topbar')
@include('layouts.sidebar')
@include('layouts.user-notification-modal')

<!--start main wrapper-->
<main class="main-wrapper">
    <div class="main-content">

        @yield('content')

    </div>
</main>
<!--end main wrapper-->

<!--start overlay-->
    <div class="overlay btn-toggle"></div>
<!--end overlay-->





<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  @include('layouts.vendor-scripts')

  @yield('scripts')

</body>

</html>
