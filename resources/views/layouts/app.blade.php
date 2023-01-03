<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/milligram.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://js.pusher.com/7.0/pusher.min.js" defer></script>
    <script type="text/javascript">
        // Fix for Firefox autofocus CSS bug
        // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
    </script>
    <script type="text/javascript" src="{{ asset('js/app.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('js/show_sidebar.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('js/feed_actions.js') }}" defer></script>
  <script type="text/javascript" src="{{ asset('js/like_comment.js') }}" defer></script>
  <script type="text/javascript" src="{{ asset('js/add_comment.js') }}" defer></script>
  <script type="text/javascript" src="{{ asset('js/reply_comment.js') }}" defer></script>
  <script type="text/javascript" src="{{ asset('js/edit_comment.js') }}" defer></script>

  </head>
  <body>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <main>
      <header>
        <div class="menu-toggle">
          <div class="hamburger">
            <span></span>
          </div>
        </div>
        <a @if (Auth::check()) href="{{ url('events') }}" @else href="{{ url('/') }}" @endif><img id="logo" src="/../logo.png" alt="logo"></a>
  
        @if (Auth::check())
        <a href="/profile/{{Auth::user()->id}}">
          @if (empty(Auth::user()->picture)) 
          <img class="mini-picture" src="/../avatars/default.png" alt="Avatar">
          @else
          <img class="mini-picture" src="/../avatars/{{Auth::user()->picture}}" alt="Avatar">
          @endif
        </a>
        @else
        <a class="button" href="{{ route('login') }}"> Login </a> 
        <a id="registerbtn" class="button register" href="{{ route('register') }}"> Register </a> 
        @endif
      </header>

      @if (!Request::is('login', 'register', '/', 'forgot_password' , 'recover_password'))
        
        @include('partials.sidebar')
        @yield('sidebar')
      @endif
      
      @if (!Request::is('login', 'register', '/', 'forgot_password' , 'recover_password'))
      <section id="content">
        @yield('content')
      </section>
      @else
      <section id="content2">
        @yield('content2')
      </section>
      @endif
      <footer  @if (!Request::is('login', 'register', '/')) id="footer" @else id="footer2" @endif class="d-flex flex-wrap justify-content-between align-items-center border-top">
          <p class="col-md-4 mb-0">© 2022 WeMeet, Inc</p>
      
          <ul class="nav col-md-4 justify-content-end">
            <li class="nav-item"><a href="/user/help" class="nav-link px-2">User help</a></li>
            <li class="nav-item"><a href="/contact_us" class="nav-link px-2">Contact us</a></li>
            <li class="nav-item"><a href="/about_us" class="nav-link px-2">About us</a></li>
          </ul>
      </footer>

    </main>
    @yield('script')
  </body>
</html>
