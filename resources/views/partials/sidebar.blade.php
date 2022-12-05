@section('sidebar')

<aside class="sidebar">
<h4>Events</h4>
<nav class="menu">
    <a href="{{ url('/events') }}" class="menu-item"><i class="bi bi-house-door-fill"></i> Home page</a>
    @if (Auth::check())
    <a href="{{ url('/myevents') }}" class="menu-item"><i class="bi bi-person-fill"></i> My events</a>
    <a href="{{ url('/calendar') }}" class="menu-item"><i class="bi bi-calendar-fill"></i> My calendar</a>
    <a class="menu-item"><i class="bi bi-bell-fill"></i> Notifications</a>
    @if (Auth::user()->is_admin)
    <a href="{{url('/manageUsers')}}" class="menu-item">Manage Users</a>
    @endif
    <a id="createButton" class="button" href="{{ route('eventsCreate') }}">Create event <i class="bi bi-plus" style="font-size:2em"></i></a>
    @endif

</nav>
<hr>
<h5>Categories</h5>

</aside>