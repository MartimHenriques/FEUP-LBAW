
@extends ('layouts.app')

@section('title', 'Event')

@section('content')

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
    <span id="info" onclick="infoFunction()" style="border-bottom: 2px solid rgba(90, 90, 90, 0.852);">Info</span>
    <span id="forum" onclick="forumFunction()" style="border-bottom: 2px solid transparent;">Forum</span>
    <a id="join" type='button' class='button' style="float:right; {{ ($attendee) ? 'background-color: CornflowerBlue' : '' }}" href="/{{($attendee) ? 'abstainEvent' : 'joinEvent'}}/{{$event->id}}">
        @if($attendee)
            Attending
        @else
            Attend
        @endif
    </a>
    
    @if ($event->visibility)
    
    <!-- Button trigger modal -->
    <button data-bs-toggle="modal" data-bs-target="#myModel" id="shareBtn" data-bs-placement="top" title="Share event!" style="float:right;">
            Share
        </button>
      
      <!-- Modal -->
        <div class="modal fade" id="myModel" tabindex="-1" aria-labelledby="myModelLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myModelLabel">Share Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>  
                        <div class="field d-flex align-items-center justify-content-between">
                            <button onclick="copyLink()" id="copyButton">Copy Link</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
    @endif
    @if (Auth::check())
        @if ($event_organizer)
            <a type='button' class="button" style="float:right;" href="/editEvent/{{$event->id}}"><i class="bi bi-pencil fs-3"></i></a>
        @endif
    @endif
    
</section>
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
                    <div class="d-flex gap-2 w-100 justify-content-between">
                        <div>
                            <h6 class="mb-0">{{$attendee->username}}</h6>
                            <p class="mb-0 opacity-75">Attendee</p>
                        </div>
                        @if($event->event_organizers()->get()->contains(Auth::user()))
                            <a href="{{route('removeFromEvent',['id_attendee'=>$attendee->id,'id_event'=>$event->id])}}">Remove</a>
                        @endif
                    </div>
                    </span>
                @endif
            @endforeach
    
        </div>
    
    </div>
</section>




<div id="forum-content" style="display: none">
    <!-- Forum List -->
    <div class=" p-2 p-sm-3 collapse forum-content show">
        @if( count($messages) < 1)
            <p>There aren't messages yet</p>
        @endif
            <div class="container mt-5">
                <div class="d-flex justify-content-center row" style="margin: 0;">
                    <div class="col-md-8">
                        @foreach($messages as $message)
                        @if($message->parent == NULL)
                        <div id="message">
                            
                                <div id="parent" class="d-flex flex-column comment-section">
                                    <div class="bg-white p-2" style="border-top-left-radius: 1em; border-top-right-radius: 1em;">
                                        <div class="d-flex flex-row user-info"><img class="profile-picture" src="/../avatars/{{$setMessage[$message->id]->picture}}">
                                            <div class="d-flex flex-column justify-content-start ml-2"><span class="d-block font-weight-bold" style="margin: 1em 0;">{{ $setMessage[$message->id]->username }}</span><span class="date text-black-50">{{ $message->date }}</span></div>
                                        </div>
                                        <div class="mt-2">
                                            <p class="comment-text">{{ $message->content }}</p>
                                        </div>
                                    </div>
                                    <div class="bg-white" style="border-bottom-left-radius: 1em; border-bottom-right-radius: 1em;">
                                        <div class="d-flex flex-row fs-12">
                                            <div class="like p-2 cursor"><i class="fa fa-thumbs-o-up"></i><span class="ml-1">Like</span></div>
                                        </div>
                                    </div>
                                    
                                </div>
                            @endif
                            @foreach ($message->messages as $son)

                            <div id="son" class="d-flex flex-column comment-section">
                                <div class="bg-white p-2" style="border-top-left-radius: 1em; border-top-right-radius: 1em;">
                                    <div class="d-flex flex-row user-info"><img class="profile-picture" src="/../avatars/{{$setMessage[$son->id]->picture}}">
                                        <div class="d-flex flex-column justify-content-start ml-2"><span class="d-block font-weight-bold" style="margin: 1em 0;">{{ $setMessage[$son->id]->username }}</span><span class="date text-black-50">{{ $message->date }}</span></div>
                                    </div>
                                    <div class="mt-2">
                                        <p class="comment-text">{{ $son->content }}</p>
                                    </div>
                                </div>
                                <div class="bg-white" style="border-bottom-left-radius: 1em; border-bottom-right-radius: 1em;"  >
                                    <div class="d-flex flex-row fs-12">
                                        <div class="like p-2 cursor"><i class="fa fa-thumbs-o-up"></i><span class="ml-1">Like</span></div>
                                    </div>
                                </div>
                                
                            </div>
                            
                            @endforeach
                        </div>
                        
                        @endforeach
                    </div>
                </div>
            </div>


    </div>
    <!-- /Forum List -->
</div>

<script>

function infoFunction() {
    var x = document.getElementById("forum-content");
    var y = document.getElementById("info-content");
    document.getElementById("info").style.borderBottomColor = "rgba(90, 90, 90, 0.852)";
    document.getElementById("forum").style.borderBottomColor = "transparent";
    x.style.display = "none";
    y.style.display = "block";

}
function forumFunction() {
    var x = document.getElementById("forum-content");
    var y = document.getElementById("info-content");
    document.getElementById("info").style.borderBottomColor = "transparent";
    document.getElementById("forum").style.borderBottomColor = "rgba(90, 90, 90, 0.852)";
    x.style.display = "block";
    y.style.display = "none";
}
function copyLink(){
    
    /* ";
    
    var dummy = document.createElement('input'),
    text = window.location.href;
    document.body.appendChild(dummy);
    dummy.value = text;
    dummy.select();
    document.execCommand("copy");
    document.body.removeChild(dummy);

     // Alert the copied text
     alert("Copied the text: " + dummy.value); */



    var btn = document.getElementById("copyButton");
    btn.innerHTML = 'copied';
    btn.style.backgroundColor = "green"

    var copyText = document.getElementById("copyText");
    navigator.clipboard.writeText(window.location.href); 
}

const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
const php_var = urlParams.get('showModal');
var myModal = new bootstrap.Modal(document.getElementById('myModal'), {});
if (php_var == true) {
    myModal.toggle();
}

</script>
@endsection