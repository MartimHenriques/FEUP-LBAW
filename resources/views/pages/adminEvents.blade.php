@extends ('layouts.app')

@section('title', 'AdminEvents')

@section('content')

<h3>Events</h3>

<table class="table table-striped">
    <tr>
        <th>Event</th>
        <th> </th>
      </tr>
    @foreach($events as $event)
    
            <tr>
                <td><a href="/events/{{ $event->id}}">{{$event->title}}</a></td> 
                <td>
                    <button data-bs-toggle="modal" data-bs-target="#myModel_<?php echo $event['id']; ?>" id="shareBtn" data-bs-placement="top" title="Delete Event" style="float:middle;">
                        Delete
                    </button>
                            
                    <!-- Modal -->
                    <div class="modal fade" id="myModel_<?php echo $event['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="myModelLabel">Delete Event</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                    <div>
                                        <h5>Warning</h5>
                                        <p>All interections of '{{$event->title}}' will be eliminated.</p>
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