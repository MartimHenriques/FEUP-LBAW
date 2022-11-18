@extends ('layouts.app')

@section('title', 'Feed')

@section('content')

<h1>Feed</h1>
<div class="event-feed">
    @each('partials.eventCard', $events, 'event')
    
</div>
@endsection


