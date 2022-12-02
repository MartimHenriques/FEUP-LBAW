@extends ('layouts.app')

@section('title', 'AdminEvents')

@section('content')

<h1>Admin Page</h1>
<h3>Manage Events</h3>
<table class="table table-striped">
<th>Events</th>

    @foreach($events as $event)
            <tr>
                <td><a href="/events/{{ $event->id}}">{{$event->title}}</a></td> <td><a class="button" {{--href="{{route('',['id'=>$user->id])}}--}}>Delete Event</a></td>
            </tr>
    @endforeach

</table>
@endsection