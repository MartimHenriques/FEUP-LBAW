@extends ('layouts.app')

@section('title', 'Result search')

@section('content')


<div class="events">
            
    @each('partials.eventCard', $supliers, 'event')

    
</div>
@endsection