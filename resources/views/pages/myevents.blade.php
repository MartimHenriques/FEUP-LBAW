@extends ('layouts.app')

@section('title', 'My events')

@section('content')

<h1>My events</h1>
<div class="myevents">
    @each('partials.eventCard', $myevents, 'event')
    
</div>
@endsection