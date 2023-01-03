@extends ('layouts.app')

@section('title', 'Calendar')

@section('content')

<h2>Events attended</h2>
<div class="eventsAttended">
    @if(count($eventsattended)>0)
    @each('partials.eventCard', $eventsattended, 'event')
    @else
    <p>You haven't been to any event yet</p>
    @endif
    
</div>
<h2>Events to attend</h2>
@if(count($eventstoattend)>0)
    <div class="eventsToAttend">
        @each('partials.eventCard', $eventstoattend, 'event')
    </div>
@else
    <p>You don't have events to attend</p>
@endif
@endsection