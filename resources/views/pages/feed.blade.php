@extends ('layouts.app')

@section('title', 'Feed')

@section('content')

<div class="input-group rounded w-50">
    <form action="api/eventsSearch" method="POST">
        @csrf
        <input type="search" name="search" id="eventSearch" class="form-control rounded" placeholder="Search" aria-label="Search" aria-describedby="search-addon" style="font-size:17px;" />
        <button type='submit' name="button" value="searchEvent">
            <i class="bi bi-search"></i>
    </button>
    </form>
</div>
<div class="event-feed" id="eventFeed">
    @foreach($events as $event)
        <div class="eventCard" data-id="{{ $event->id }}">

        <a href="/events/{{ $event->id}}">
            <img src="/../img_events/{{ $event->picture}}" alt="event picture" id="eventMiniPicture">
            <div class="event-info">
            <p id="title">{{ $event->title }}</p>
            <p id="local">{{$event->local}}</p>
            <p>{{$event->start_date}}</p>
            </div>
        </a>
        <div>
            @if ($event->visibility)
        <!-- Button trigger modal -->
            <button id="copyButton" onclick="copyLinkFeed({{$event->id}});">Share</button>
            <a id="join" type='button' class='button' style="float:right; {{ ($attendee[$event->id]) ? 'background-color: CornflowerBlue' : '' }}" href="/{{($attendee[$event->id]) ? 'abstainEvent' : 'joinEvent'}}/{{$event->id}}">
            @if($attendee[$event->id])
                Showing up
            @else
                Show up
            @endif
        </a>
        @endif
        </div>
        
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
/*
    const eventsearch = document.getElementById("eventSearch");
    eventsearch.addEventListener("keyup", searchEvent);
    function searchEvent() {
        sendAjaxRequest('post', '/api/eventsSearch', {search: eventsearch.value}, eventSearchHandler);
    }
    function eventSearchHandler() {
        let events = JSON.parse(this.responseText);
        //console.log(events);
        let body = document.getElementById("eventFeed");
        body.innerHTML = "";

        for(let event of events) {
            document.body.innerHTML += '<div  class="eventCard" data-id="' + event['id'] + '></div>';
            document.body.innerHTML += '<a href="/events/' + event['id'] + '">';  
            document.body.innerHTML += '<div class="event-info">';
            document.body.innerHTML += '<h2>'+ event['title'] +'</h2>';
            if(event['visibility'])
                document.body.innerHTML += '<h5>Public</h5>';
            else
                document.body.innerHTML += '<h5>Private</h5>';
            document.body.innerHTML += '<h5>Local: '+ event['local'] +'</h5>';
            document.body.innerHTML += '<h5>'+ event['start_date'] +'</h5>';
            if(authCheck){
                if(eventOrganizer[event['id']-1])
                    document.body.innerHTML += '<a type='button' href="/editEvent/'+ event['id'] +'"><i class="bi bi-pencil"></i></a>'; 
            }
            document.body.innerHTML += '</div>';
            document.body.innerHTML += '</a>';
            if(event['visibility'])
                document.body.innerHTML += '<button id="copyButton" onclick="copyLinkFeed('+event['id']+');">Share</button>';

        }
    }*/

</script>
@endsection


