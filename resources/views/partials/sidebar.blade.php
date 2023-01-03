
@section('sidebar')

<aside class="sidebar">
    <div class="input-group rounded" style="margin-top: 7em">
        <form action="api/eventsSearch" method="POST">
            @csrf
            <label style="font-size: 1.3em">Search events</label>
            <input type="search" name="search" id="eventSearch" class="form-control rounded" placeholder="Search" aria-label="Search" style="font-size:17px;" />
            <button type='submit' name="button" value="searchEvent" style="display:none;" disabled>
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>
<h4>Events</h4>
<nav class="menu">
    <a href="{{ url('/events') }}" class="menu-item"><i class="bi bi-house-door-fill"></i> Home page</a>
    @if (Auth::check())
    @if (Auth::user()->is_admin)
    <h4>Administration</h4>
    <a href="{{url('/manage/users')}}" class="menu-item"><i class="bi bi-person-fill-gear"></i>Users</a>
    <a href="{{url('/manage/events')}}" class="menu-item"><i class="bi bi-calendar-event-fill"></i>Events</a>
    <a href="{{url('/manage/reports')}}" class="menu-item"><i class="bi bi-exclamation-circle-fill"></i>Reports</a>
    @else
    <a href="{{ url('/my_events') }}" class="menu-item"><i class="bi bi-person-fill"></i> My events</a>
    <a href="{{ url('/calendar') }}" class="menu-item"><i class="bi bi-calendar-fill"></i> My calendar</a>
    <a href="{{ url('/notifications') }}" class="menu-item"><i class="bi bi-bell-fill"></i> Notifications</a>
    @endif
    <a id="createButton" class="button" href="/events_create">Create event <i class="bi bi-plus" style="font-size:2em"></i></a>
    @endif

</nav>
<hr>

</aside>