@extends ('layouts.app')

@section('title', 'Feed')

@section('content')

<h1>Feed</h1>
<div class="input-group rounded w-50">
    <input type="search" name="search" id="eventSearch" class="form-control rounded" placeholder="Search" aria-label="Search" aria-describedby="search-addon" style="font-size:17px;" />
    <button type='submit' class='button' href="api/eventsSearch">
        <i class="bi bi-search"></i>
    </button>
</div>
<div class="event-feed" id="eventFeed">
    @foreach($events as $event)
        <div class="eventCard" data-id="{{ $event->id }}">

        <a href="/events/{{ $event->id}}">
            <div class="event-info">
            <h2>{{ $event->title }}</h2>
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
    }

</script>
@endsection


