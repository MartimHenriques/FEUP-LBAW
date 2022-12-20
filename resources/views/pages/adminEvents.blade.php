@extends ('layouts.app')

@section('title', 'AdminEvents')

@section('content')

<h1>Admin Page</h1>
<h3>Events</h3>

<table class="table table-striped">
    @foreach($events as $event)
            <tr>
                <td><a href="/events/{{ $event->id}}">{{$event->title}}</a></td> 
                <td>
                    <button data-bs-toggle="modal" data-bs-target="#myModel" id="shareBtn" data-bs-placement="top" title="Delete Event" style="float:middle;">
                        Delete
                    </button>
                            
                    <!-- Modal -->
                    <div class="modal fade" id="myModel" tabindex="-1" aria-labelledby="myModelLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="myModelLabel">Delete Event</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                    <div>
                                        <h5>Atenção</h5>
                                        <p>Todos as interações de '{{$event->title}}' vão ser eliminadas.</p>
                                    </div>
                                    <div class="field d-flex align-items-center justify-content-between">
                                        <button onclick="window.location='{{route('deleteEvent',['id'=>$event->id])}}'" id="deleteEventButton" style="float:middle;">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a></td>
            </tr>
    @endforeach
</table>
<div class="text-center">
    {!! $events->links(); !!}
</div>
@endsection