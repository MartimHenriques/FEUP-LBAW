@extends ('layouts.app')

@section('title', 'Calendar')

@section('content')

<h1>Events attended/to attend</h1>
<div class="myevents">
    @each('partials.eventCard', $myevents, 'event')
    
</div>
@endsection