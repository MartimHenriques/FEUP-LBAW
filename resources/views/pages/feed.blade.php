@extends ('layouts.app')

@section('title', 'Feed')

@section('content')

<div class="input-group rounded w-50">
    <form action="api/eventsSearch" method="POST">
        @csrf
        <input type="search" name="search" id="eventSearch" class="form-control rounded" placeholder="Search" aria-label="Search" aria-describedby="searcon" style="font-size:17px;" />
        <button type='submit' name="button" value="searchEvent" style="display:none;" disabled>
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
@endsection

@section('script')
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
            console.log(event);
            let div_eventCard = document.createElement("div");
            div_eventCard.setAttribute('class', 'eventCard');
            div_eventCard.setAttribute('data-id', event['id']);

            let h = document.createElement('a');
            h.setAttribute('href', "/events/"+event['id']);

            let picture = document.createElement('img');
            picture.setAttribute('src', "/../img_events/"+event['picture']);
            picture.setAttribute('alt', "event picture");
            picture.setAttribute('id', "eventMiniPicture");
            h.appendChild(picture);

            let div_cardInfo = document.createElement("div");
            div_cardInfo.setAttribute('class', 'event-info');

            let title = document.createElement("p");
            title.setAttribute('id', 'title');
            title.innerHTML = event['title'];
            div_cardInfo.appendChild(title)
            
            let local = document.createElement("p");
            local.setAttribute('id', 'local');
            local.innerHTML = event['local'];
            div_cardInfo.appendChild(local);

            let start_date = document.createElement("p");
            start_date.innerHTML = event['start_date'];
            div_cardInfo.appendChild(start_date);
            
            h.appendChild(div_cardInfo);
            div_eventCard.appendChild(h);

            let div_button = document.createElement("div");
            if(event['visibility']) {
                let btn = document.createElement('button');
                btn.setAttribute('id', "copyButton")
                btn.setAttribute('onclick', "copyLinkFeed("+event['id']+")");
                btn.innerHTML = "Share";
                div_button.appendChild(btn);


                let a_link = document.createElement('a');
                a_link.setAttribute('id', 'join');
                a_link.setAttribute('type', 'button');
                a_link.setAttribute('class', 'button');
                
                if ("{!! $attendee[$event->id] !!}") {
                    a_link.setAttribute('style', "float:right; background: CornflowerBlue");
                    a_link.setAttribute('href', "/abstainEvent"+$event['id']);
                    a_link.innerHTML = "Showing up";
                } else {
                    a_link.setAttribute('style', "float:right;");
                    a_link.setAttribute('href', "/joinEvent" + event['id']);
                    a_link.innerHTML = "Show up";   
                }
                
                div_button.appendChild(a_link);
            }
            div_eventCard.appendChild(div_button);
            body.appendChild(div_eventCard);
        }
    }

</script>
@endsection


