@extends ('layouts.app')

@section('title', 'Calendar')

@section('content')

<h1>Events attended</h1>
<div class="eventsAttended">
    @if(count($eventsattended)>0)
    @each('partials.eventCard', $eventsattended, 'event')
    @else
    <p>You haven't been to any event yet</p>
    @endif
    
</div>
<h1>Events to attend</h1>
<div class="eventsToAttend">
    @if(count($eventstoattend)>0)
    @each('partials.eventCard', $eventstoattend, 'event')
    @else
    <p>You don't have events to attend</p>
    @endif
</div>
@endsection