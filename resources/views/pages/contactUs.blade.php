@extends ('layouts.app')

@section('title', 'Contact Us')

@section('content')

<h1>Contact Us</h1>
@if(count($myevents) < 1)
    <p>You haven't created any events yet.</p>
@endif
<div class="myevents">
    @each('partials.eventCard', $myevents, 'event')
    
</div>
@endsection