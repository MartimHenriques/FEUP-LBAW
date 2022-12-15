@extends ('layouts.app')

@section('title', 'AdminEvents')

@section('content')

<h1>Admin Page</h1>
<h3>Events</h3>

<table class="table table-striped">
    @foreach($events as $event)
            <tr>
                <td><a href="/events/{{ $event->id}}">{{$event->title}}</a></td> <td><a class="button" href="{{route('deleteEvent',['id'=>$event->id])}}">Delete Event</a></td>
            </tr>
    @endforeach
</table>
<div class="text-center">
    {!! $events->links(); !!}
</div>
@endsection