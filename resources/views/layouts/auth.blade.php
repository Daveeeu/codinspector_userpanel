<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="{{ URL::asset('build/images/favicon-32x32.png') }}" type="image/png">
    <title>@yield('title') | Laravel & Bootstrap 5 Admin Dashboard Template</title>

    @yield('css')

    @include('layouts.head-css')

</head>

<body>
    @yield('content')

<!--start overlay-->
<div class="overlay btn-toggle"></div>
<!--end overlay-->

  @include('layouts.vendor-scripts')
  <script>
      $(document).ready(function () {
          $("#show_hide_password_1 a, #show_hide_password_2 a").on('click', function (event) {
              event.preventDefault();
              var passwordField = $(this).closest('.input-group');
              var passwordInput = passwordField.find('input');
              var icon = passwordField.find('i');

              if (passwordInput.attr("type") === "text") {
                  passwordInput.attr('type', 'password');
                  icon.addClass("bi-eye-slash-fill");
                  icon.removeClass("bi-eye-fill");
              } else if (passwordInput.attr("type") === "password") {
                  passwordInput.attr('type', 'text');
                  icon.removeClass("bi-eye-slash-fill");
                  icon.addClass("bi-eye-fill");
              }
          });
      });

  </script>

  @yield('scripts')


</body>

</html>
