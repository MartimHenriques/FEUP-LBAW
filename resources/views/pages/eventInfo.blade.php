
@extends ('layouts.app')

@section('title', 'Event')

@section('content')

@include('partials.eventHeader')
@yield('eventHeader')

<section id="info-content">
    <div id="details" >
        <h4>Details</h4>
        <p>{{ $event->description }}</p>
        @if ($event->visibility)
            <span style="display:block;"><i class="bi bi-globe-asia-australia"></i><p style="display:inline;">  Public</p></span>
        @else
            <i class="bi bi-lock-fill"></i><p style="display:inline;">  Private</p>
        @endif
        <span style="display:block;"><i class="bi bi-geo-alt-fill"></i><p style="display:inline;">  {{ $event->local }}</p></span>
        @if($event->start_date != $event->final_date )
            <p>Data de início: {{ $event->start_date }}</p>
            <p>Data de fim: {{ $event->final_date }}</p>
        @else
            <p>Data: {{ $event->start_date }}</p>
        @endif
            
    </div>
    <div id="attendeeslist">
        <h4>Attendees</h4>
        <h5><strong>{{count($event->attendees()->get())}}</strong> people are attending</h5>
        <div class="list-group w-auto">
            @foreach($event->attendees()->get() as $attendee)
                @if( $event->event_organizers()->get()->contains($attendee))
                <span class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                <img src="/../avatars/{{$attendee->picture}}" alt="twbs" width="32" height="32" class="rounded-circle flex-shrink-0">
                <div class="d-flex gap-2 w-100 justify-content-between">
                    <div>
                        <h6 class="mb-0">{{$attendee->username}}</h6>
                        <p class="mb-0 opacity-75">Organizer</p>
                    </div>
                </div>
                </span>
                @endif
            @endforeach
            @foreach($event->attendees()->get() as $attendee)
                @if( !($event->event_organizers()->get()->contains($attendee)))
                <span class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                    <img src="/../avatars/{{$attendee->picture}}" alt="twbs" width="32" height="32" class="rounded-circle flex-shrink-0">
                    <div id="attendeeInfo" class="d-flex gap-2 w-100 justify-content-between">
                        <div>
                            <h6 class="mb-0">{{$attendee->username}}</h6>
                            <p class="mb-0 opacity-75">Attendee</p>
                        </div>
                        @if($event->event_organizers()->get()->contains(Auth::user()))
                            <div>
                                <a href="{{route('makeAnOrganizer',['id_user'=>$attendee->id,'id_event'=>$event->id])}}" style="margin-right: 1em">Turn into an organizer</a>
                                <a href="{{route('removeFromEvent',['id_attendee'=>$attendee->id,'id_event'=>$event->id])}}">Remove</a>
                            </div>
                            
                        @endif
                    </div>
                    </span>
                @endif
            @endforeach
    
        </div>
    
    </div>
</section>

@endsection