<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description"
    content="Online Helpdesk | Customer Support Ticket System,{{ config('app.name') }} support Enterprise service desk features like Email piping, knowledgebase">
  <meta name="keywords" content="Ticket System, Customer Support, CRM System, ERP System, Online Helpdesk">
  <meta name="author" content="TecSee.de">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Alfacockpit') }}</title>

  <!-- Favicon -->
  <link href="{{ asset('frontend/img/brand/favicon.png') }}" rel="icon" type="image/png">
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,700" rel="stylesheet">
  <link href="{{ asset('frontend/fonts/icomoon/style.css') }}" rel="stylesheet">
  <link href="{{ asset('frontend/fonts/flaticon/flaticon.css') }}" rel="stylesheet">

  <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('frontend/css/jquery-ui.css') }}" rel="stylesheet">
  <link href="{{ asset('frontend/css/owl.carousel.min.css') }}" rel="stylesheet">
  <link href="{{ asset('frontend/css/owl.theme.default.min.css') }}" rel="stylesheet">
  <link href="{{ asset('frontend/css/jquery.fancybox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('frontend/css/bootstrap-datepicker.css') }}" rel="stylesheet">
  <link href="{{ asset('frontend/css/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet">
  <link href="{{ asset('frontend/css/custom.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
  @stack('extra-css')
  @yield('extra-style')
</head>

<body data-spy="scroll" data-target=".site-navbar-target" data-offset="300">
  <div id="overlayer"></div>
  <div class="loader">
    <div class="spinner-border text-primary" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>

  <div class="site-wrap">
    <div class="site-mobile-menu site-navbar-target">
      <div class="site-mobile-menu-header">
        <div class="site-mobile-menu-close mt-3">
          <span class="icon-close2 js-menu-toggle"></span>
        </div>
      </div>
      <div class="site-mobile-menu-body"></div>
    </div>
    <header class="site-navbar py-4 js-sticky-header site-navbar-target" role="banner">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-6 col-md-3 col-xl-4  d-block">
            <h1 class="mb-0 site-logo"><a href="/"
                class="text-black h2 mb-0">{{ config('app.name', 'Alfacockpit') }}<span class="text-primary">.</span>
              </a></h1>
          </div>
          <div class="col-12 col-md-9 col-xl-8 main-menu">
            <nav class="site-navigation position-relative text-right" role="navigation">
              <ul class="site-menu main-menu js-clone-nav mr-auto d-none d-lg-block ml-0 pl-0">
                <li><a href="/" class="nav-link">Home</a></li>
                <li><a href="#features-section" class="nav-link">Eigenschaften</a></li>
                <li><a href="#price-section" class="nav-link">Preise</a></li>
                <li>
                  <a href="#about-section" class="nav-link">Über uns</a>
                </li>
                <li><a href="#contact-section" class="nav-link">Kontakt</a></li>
              </ul>
            </nav>
          </div>
          <div class="col-6 col-md-9 d-inline-block d-lg-none ml-md-0"><a href="#"
              class="site-menu-toggle js-menu-toggle text-black float-right"><span class="icon-menu h3"></span></a>
          </div>
        </div>
      </div>
    </header>

    @yield('content')

    <div class="footer py-5 text-center">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <ul class="list-inline">
              <li class="list-inline-item"><a class="social-icon text-xs-center" href="{{ route('agb') }}">AGB</a></li>
              <li class="list-inline-item"><a class="social-icon text-xs-center" href="{{ route('impressum') }}">impressum</a></li>
            </ul>
            <p class="mb-0">
              Copyright &copy;<script>
                document.write(new Date().getFullYear());
              </script> All rights reserved | <a href="https://agency-it.com" target="_blank">Agency-it.com</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Core -->
  <script src="{{ asset('frontend/js/jquery-3.3.1.min.js') }}"></script>
  <script src="{{ asset('frontend/js/jquery-ui.js') }}"></script>
  <script src="{{ asset('frontend/js/popper.min.js') }}"></script>
  <script src="{{ asset('frontend/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('frontend/js/owl.carousel.min.js') }}"></script>
  <script src="{{ asset('frontend/js/jquery.countdown.min.js') }}"></script>
  <script src="{{ asset('frontend/js/bootstrap-datepicker.min.js') }}"></script>
  <script src="{{ asset('frontend/js/jquery.easing.1.3.js') }}"></script>
  <script src="{{ asset('frontend/js/aos.js') }}"></script>
  <script src="{{ asset('frontend/js/jquery.fancybox.min.js') }}"></script>
  <script src="{{ asset('frontend/js/jquery.sticky.js') }}"></script>
  <script src="{{ asset('frontend/js/main.js') }}"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
  @stack('extra-js')
  <script>
    jQuery(function(){
      $('input.timepicker').timepicker({
        'timeFormat': 'HH:mm'
      });
    });
  </script>
  @yield('extra-scripts')
</body>

</html>