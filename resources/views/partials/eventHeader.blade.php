<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Event created successfully!</h3>
            <div class="modal-body">
            <h5>You can access it in yours events.</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
        </div>
        </div>
    </div>
</div>


<section id="eventHeader">
    <img src="/../img_events/{{$event->picture}}" alt="event picture" id="eventPicture" style="width: 40%;">
    <h3>{{ $event->title }}</h3>
    @if($event->is_canceled)
        <h2 style="text-align:center; color: red; font-weight:bold">Event canceled</h2>
    @endif
    <a id="info" href="/events/{{$event->id}}/info" style="">Info</a>
    <a id="forum" href="/events/{{$event->id}}/forum" style="">Forum</a>
    @if(!$event->is_canceled)
         <!-- Button trigger modal -->
        <button style="float:right;" type="button" data-toggle="modal" data-target="#exampleModalCenter">
            Report
        </button>
        
        <!-- Modal -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-body">
                            <p style="color: red">Report event</p>
                            <form action="/create/report/{{$event->id}}" method="POST" style="margin-bottom: 0">
                                @csrf
                                <label>Why are you reporting this event?</label>
                                <input type="text" name="motive" placeholder="Motive" required>
                                <button type="submit">
                                    Send
                                </button>
                            </form>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-body">
                            
                            @if(count($event->event_organizers) == 1 && $event_organizer)
                            <p>You are the only event organizer. If you leave, the event will be canceled!</p>
                            @endif
                            Are you sure you want to leave?
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a class='button' href="/{{($attendee) ? 'abstain/event' : 'join/event'}}/{{$event->id}}">Leave event</a>
                    </div>
                    
                </div>
            </div>
        </div>
        @if (Auth::check())
        <a @if($attendee) data-toggle="modal" data-target="#cancelModal" @else href="/{{($attendee) ? 'abstain/event' : 'join/event'}}/{{$event->id}}" @endif id="join" class='button' style="float:right; {{ ($attendee) ? 'background-color: CornflowerBlue' : '' }}" >
        @else 
        <a id="join" data-bs-toggle="modal" data-bs-target="#myModalLog" id="attend1" data-bs-placement="top" title="Log In Needed"  class='button' style="float:right; {{ ($attendee) ? 'background-color: CornflowerBlue' : '' }}" href="/{{($attendee) ? 'abstain/event' : 'join/event'}}/{{$event->id}}">
        @endif
        @if($attendee)
            Leave Event
        @else
            Attend
        @endif
        </a>

        @if ($event->visibility)
            <a class="button" onclick="copyLink()" id="copyButton" style="float:right;">Share</a>
        @endif
        @if (Auth::check())
            @if ($event_organizer)
                @if(!$event->visibility)
                    <a class="button" style="float:right;" href="/event/{{$event->id}}/invite">Invite</a>
                @endif
                <a class="button" style="float:right;" href="/editEvent/{{$event->id}}"><i class="bi bi-pencil fs-3"></i></a>
            @endif
        @endif

        <div class="modal fade" id="myModalLog" tabindex="-1" role="dialog" aria-labelledby="Share event!!" aria-hidden="true">
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
            btn.style.backgroundColor = "#9bb6fcf6";
        }, 1000);
    }
    
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const php_var = urlParams.get('showModal');
    var myModal = new bootstrap.Modal(document.getElementById('myModal'), {});
    if (php_var == true) {
        myModal.toggle();
    }
    
</script>

