@extends ('layouts.app')

@section('title', 'Feed')

@section('content')

<div class="event-feed">
    @foreach($events as $event)
        <div class="eventCard" data-id="{{ $event->id }}">

        <a href="/events/{{ $event->id}}">
            <div class="event-info">
            <h4>{{ $event->title }}</h4>
            @if ($event->visibility)
                <h5>Public</h5>
            @else
                <h5>Private</h5>
            @endif
            <h5>Local: {{$event->local}}</h5>
            <h5>{{$event->start_date}}</h5>
            @if (Auth::check())
                @if ($event_organizer[($event->id)])
                    <a type='button' href="/editEvent/{{$event->id}}"><i class="bi bi-pencil"></i></a>
                @endif
            @endif
            </div>
        </a>

        @if ($event->visibility)
        <!-- Button trigger modal -->
        <button id="copyButton" onclick="copyLinkFeed({{$event->id}});">Share</button>
        @endif
    </div>
    @endforeach 
</div>

<script>
  function copyLinkFeed(id){
    var dummy = document.createElement('input'),
    text = window.location.href + "/" + id;
    document.body.appendChild(dummy);
    dummy.value = text;
    dummy.select();
    document.execCommand('copy');
    document.body.removeChild(dummy);

    // Alert the copied text
    alert("Copied the text: " + dummy.value); 
  }
</script>
@endsection


