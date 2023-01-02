
@section('sidebar')

<aside class="sidebar">
<h4>Events</h4>
<nav class="menu">
    <a href="{{ url('/events') }}" class="menu-item"><i class="bi bi-house-door-fill"></i> Home page</a>
    @if (Auth::check())
    @if (Auth::user()->is_admin)
    <h4>Administration</h4>
    <a href="{{url('/manageUsers')}}" class="menu-item"><i class="bi bi-person-fill-gear"></i>Users</a>
    <a href="{{url('/manageEvents')}}" class="menu-item"><i class="bi bi-calendar-event-fill"></i>Events</a>
    <a href="{{url('/manageReports')}}" class="menu-item"><i class="bi bi-exclamation-circle-fill"></i>Reports</a>
    @else
    <a href="{{ url('/myevents') }}" class="menu-item"><i class="bi bi-person-fill"></i> My events</a>
    <a href="{{ url('/calendar') }}" class="menu-item"><i class="bi bi-calendar-fill"></i> My calendar</a>
    <a href="{{ url('/notifications') }}" class="menu-item"><i class="bi bi-bell-fill"></i> Notifications</a>
    @endif
    <a id="createButton" class="button" href="{{ route('eventsCreate') }}">Create event <i class="bi bi-plus" style="font-size:2em"></i></a>
    @endif

</nav>
<hr>
<h5>Categories</h5>
<ul>

</ul>
</aside>