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
    <script src="/js/search.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        // Fix for Firefox autofocus CSS bug
        // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
    </script>
    <script type="text/javascript" src="{{ asset('js/app.js') }}" defer>
</script>

  </head>
  <body>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <main>
      <header>
        <a href="{{ url('/') }}"><img id="logo" src="/../logo.png" alt="logo"></a>

        <input type="search" class="form-control" placeholder="Search..." aria-label="Search" id="searchbar">
        <div id = "searchResults">
          <h2 id = "eventsTitleSearch" style="display: none;"></h2>
          <div id = "eventsSearch"></div>
        </div>
  
        
        @if (Auth::check())
        <a class="button" href="{{ url('/profile') }}"> Profile </a>
        <a class="button" href="{{ url('/logout') }}"> Logout </a> <span>{{ Auth::user()->name }}</span>
        @else
        <a class="button" href="{{ route('login') }}"> Login </a> 
        <a class="button" href="{{ route('register') }}"> Register </a> 
        @endif
      </header>
      <div class="menu-toggle">
        <div class="hamburger">
          <span></span>
        </div>
      </div>
  
      <aside class="sidebar">
        <nav class="menu">
            <a href="{{ url('/events') }}" class="menu-item">Events feed</a>
            @if (Auth::check())
            <a href="{{ url('/myevents') }}" class="menu-item">My events</a>
            <a href="{{ url('/calendar') }}" class="menu-item">My calendar</a>
            @if (Auth::user()->is_admin)
            <a href="{{url('/manageUsers')}}" class="menu-item">Manage Users</a>
            @endif
            <a id="createButton" class="button" href="{{ route('eventsCreate') }}">Create event<i class="bi bi-plus" style="font-size:2em"></i></a>
            @endif

        </nav>
  
      </aside>

      <section id="content">
        @yield('content')
      </section>
    </main>
    <script>
      const menu_toggle = document.querySelector('.menu-toggle');
      const sidebar = document.querySelector('.sidebar');
  
      menu_toggle.addEventListener('click', () => {
        menu_toggle.classList.toggle('is-active');
        sidebar.classList.toggle('is-active');
      });
    </script>
  </body>
</html>
