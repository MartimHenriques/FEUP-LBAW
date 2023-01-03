@extends ('layouts.app')

@section('title', 'My events')

@section('content')

<h1>My events</h1>

@if(count($myevents) < 1)
    <p>You haven't created any events yet.</p>
@endif

    <div class="myevents">
        @each('partials.eventCard', $myevents, 'event')
    </div>

@endsection