
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <script src="{{ mix('dist/js/app.js') }}"></script>
      {!! NoCaptcha::renderJs() !!}
</head>

<body class="hold-transition login-page">
<div class="login-box" id="app">
  <div class="login-logo">
    <a href="#">ALFerp</a>
  </div>
  <!-- /.login-logo -->
  @yield('content')
</div>
<!-- /.login-box -->

</body>
</html>
