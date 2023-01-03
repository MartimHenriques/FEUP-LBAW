<div id="myModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Event created successfully!</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5>You can access it in yours events.</h5>
            </div>
        </div>
    </div>
</div>


<section id="eventHeader">
    <img src="/../img_events/{{$event->picture}}" alt="event picture" id="eventPicture" style="width: 40%; aligns-items: center;">
    <h3>{{ $event->title }}</h3>
    <a id="info" href="/events/{{$event->id}}/info" style="">Info</a>
    <a id="forum" href="/events/{{$event->id}}/forum" style="">Forum</a>
    <a id="join" type='button' class='button' style="float:right; {{ ($attendee) ? 'background-color: CornflowerBlue' : '' }}" href="/{{($attendee) ? 'abstainEvent' : 'joinEvent'}}/{{$event->id}}">
        @if($attendee)
            Attending
        @else
            Attend
        @endif
    </a>
    
    @if ($event->visibility)
    
    <a class="button" onclick="copyLink()" id="copyButton" style="float:right;">Share</a>

    @endif
    @if (Auth::check())
        @if ($event_organizer)
            <a type='button' class="button" style="float:right;" href="/editEvent/{{$event->id}}"><i class="bi bi-pencil fs-3"></i></a>
        @endif
    @endif
    
</section>
<script>

    if(window.location.href.indexOf("info")>-1){
        document.getElementById("info").style.borderBottom = "2px solid rgba(90, 90, 90, 0.852)";
    }
    else if(window.location.href.indexOf("forum")>-1){
        document.getElementById("forum").style.borderBottom = "2px solid rgba(90, 90, 90, 0.852)";
    }
    

    function copyLink(){
        var btn = document.getElementById("copyButton");
        btn.innerHTML = 'link copied';
        btn.style.backgroundColor = "green"
    
        var copyText = document.getElementById("copyText");
        navigator.clipboard.writeText(window.location.href); 

        setTimeout(function(){
            btn.innerHTML = 'share';
            btn.style.backgroundColor = "CornflowerBlue"
        }, 2000);
    }
    
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const php_var = urlParams.get('showModal');
    var myModal = new bootstrap.Modal(document.getElementById('myModal'), {});
    if (php_var == true) {
        myModal.toggle();
    }
    
</script>

