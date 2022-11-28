@extends ('layouts.app')

@section('title', 'Feed')

@section('content')

<div class="event-feed">
    @each('partials.eventCard', $events, 'event')
    
</div>
@endsection


