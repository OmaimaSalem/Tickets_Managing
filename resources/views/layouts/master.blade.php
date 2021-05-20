<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
    <script type="text/javascript">
    @auth
      window.Roles = {!! json_encode(Auth::user()->allRoles, true) !!};
      window.Permissions = {!! json_encode(Auth::user()->allPermissions, true) !!};
    @else
      window.Roles = [];
      window.Permissions = [];
    @endauth
    </script>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper" id="app">
      <layout></layout>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->

    <script src="{{ mix('dist/js/app.js') }}"></script>

</body>

</html>
