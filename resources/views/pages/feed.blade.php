@extends ('layouts.app')

@section('title', 'Feed')

@section('content')


<div class="event-feed" id="eventFeed">
    @foreach($events as $event)
        @if ( ($event->visibility || $attendee[$event->id]) && !$event->is_canceled)

        <div class="eventCard" data-id="{{ $event->id }}">

            <a href="/events/{{ $event->id}}/info">
                <img src="/../img_events/{{ $event->picture}}" alt="event picture" class="eventMiniPicture">
                <div class="event-info">
                    <p class="title">{{ $event->title }}</p>
                    <p class="local">{{$event->local}}</p>
                    <p>{{$event->start_date}}</p>
                </div>
            </a>
            <div>
                @if ($event->visibility)
                    <button id="{{$event->id}}" onclick="copyLinkFeed({{$event->id}});">Share</button>
                    @if (Auth::check())
                    <a id="join" type='button' class='button' style="float:right; {{ ($attendee[$event->id]) ? 'background-color: CornflowerBlue' : '' }}" href="/{{($attendee[$event->id]) ? 'abstain/event' : 'join/event'}}/{{$event->id}}">
                    @else <a id="join" type='button' data-bs-toggle="modal" data-bs-target="#ModalCenter" id="attend1" data-bs-placement="top" title="Log In Needed" class='button' style="float:right; {{ ($attendee[$event->id]) ? 'background-color: CornflowerBlue' : '' }}" href="/{{($attendee[$event->id]) ? 'abstain/event' : 'join/event'}}/{{$event->id}}">
                    @endif
                    @if($attendee[$event->id])
                        Attending
                    @else
                        Attend
                    @endif
                    </a>
                @endif
            </div>

            <p>{{$event->is_canceled}}</p>
        </div>


        @endif
    @endforeach 

    <!-- Modal -->
    <div class="modal fade" id="ModalCenter" tabindex="-1" role="dialog" aria-labelledby="Share event!!" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <div class="modal-body">
            You need to login first.
           </div>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>        </div>
        </div>
    </div>

</div>
@if (Auth::check())
<div class="text-center">
    {!! $events->links() !!}
</div>
@endif
@endsection

@section('script')
<script>
    const eventsearch = document.getElementById("eventSearch");
    eventsearch.addEventListener("keyup", searchEvent);
    function searchEvent() {
        sendAjaxRequest('post', '/api/eventsSearch', {search: eventsearch.value}, eventSearchHandler);
    }

    function eventSearchHandler() {
        let events = JSON.parse(this.responseText);
        let body = document.getElementById("eventFeed");
        body.innerHTML = "";
        for(let event of events) {
            console.log(event);
            let div_eventCard = document.createElement("div");
            div_eventCard.setAttribute('class', 'eventCard');
            div_eventCard.setAttribute('data-id', event['id']);

            let h = document.createElement('a');
            h.setAttribute('href', "/events/"+event['id']+"/info");

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
                btn.setAttribute('onclick', "copyLinkFeed("+event['id']+"/info)");
                btn.innerHTML = "Share";
                div_button.appendChild(btn);


                let a_link = document.createElement('a');
                a_link.setAttribute('id', 'join');
                a_link.setAttribute('type', 'button');
                a_link.setAttribute('class', 'button');
                
                if ("{!! $attendee[$event->id] !!}") {
                    a_link.setAttribute('style', "float:right; background: CornflowerBlue");
                    a_link.setAttribute('href', "/abstain/event"+$event['id']);
                    a_link.innerHTML = "Showing up";
                } else {
                    a_link.setAttribute('style', "float:right;");
                    a_link.setAttribute('href', "/join/event" + event['id']);
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


