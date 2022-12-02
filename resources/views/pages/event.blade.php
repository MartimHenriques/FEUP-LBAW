
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

<h1>{{ $event->title }}</h1>
<a id="join" type='button' class='button' style="{{ ($attendee) ? 'background-color: CornflowerBlue' : '' }}" href="/{{($attendee) ? 'abstainEvent' : 'joinEvent'}}/{{$event->id}}">
    @if($attendee)
        Showing up
    @else
        Show up
    @endif
</a>

@if ($event->visibility)

<!-- Button trigger modal -->
<button data-bs-toggle="modal" data-bs-target="#myModel" id="shareBtn" data-bs-placement="top" title="Share event!">
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
<br>
<span id="info" onclick="infoFunction()" style="border-bottom: 1px solid rgba(90, 90, 90, 0.852);">Info</span>
<span id="forum" onclick="forumFunction()" style="border-bottom: 1px solid white;">Forum</span>
<div id="info-content">
<p>{{ $event->description }}</p>
@if ($event->visibility)
    <h5>Public</h5>
@else
    <h5>Private</h5>
@endif
<p>Local: {{ $event->local }}</p>
@if($event->start_date != $event->final_date )
    <p>Data de início: {{ $event->start_date }}</p>
    <p>Data de fim: {{ $event->final_date }}</p>
@else
    <p>Data: {{ $event->start_date }}</p>
@endif
    <h4>Participants</h4>
    <table class="table table-striped">
    <th>Organizers</th>
    @foreach($event->attendees()->get() as $attendee)
        @if( $event->event_organizers()->get()->contains($attendee))
            <td>{{$attendee->username}}</td>
        @endif
    @endforeach
    </table>
    
    <table class="table table-striped">
    <th>Attendee</th><th></th>
    @foreach($event->attendees()->get() as $attendee)
        @if( !($event->event_organizers()->get()->contains($attendee)))
            <tr>
                <td>{{$attendee->username}}</td>
                @if($event->event_organizers()->get()->contains(Auth::user()))
                <td><a href="{{route('removeFromEvent',['id_attendee'=>$attendee->id,'id_event'=>$event->id])}}">Remove</a></td>
                @endif
            </tr>
        @endif
    @endforeach
    </table>



</div>


<div id="forum-content" style="display: none">
    <!-- Forum List -->
    <div class="inner-main-body p-2 p-sm-3 collapse forum-content show">
        @if( count($messages) < 1)
            <p>There aren't messages yet</p>
        @endif
            <div class="container mt-5">
                <div class="d-flex justify-content-center row">
                    <div class="col-md-8">
                        @foreach($messages as $message)
                        <div class="d-flex flex-column comment-section">
                            <div class="bg-white p-2">
                                <div class="d-flex flex-row user-info"><img class="profile-picture" src="/../avatars/{{$setMessage[$message->id]->picture}}">
                                    <div class="d-flex flex-column justify-content-start ml-2"><span class="d-block font-weight-bold name">{{ $setMessage[$message->id]->username }}</span><span class="date text-black-50">{{ $message->date }}</span></div>
                                </div>
                                <div class="mt-2">
                                    <p class="comment-text">{{ $message->content }}</p>
                                </div>
                            </div>
                            <div class="bg-white">
                                <div class="d-flex flex-row fs-12">
                                    <div class="like p-2 cursor"><i class="fa fa-thumbs-o-up"></i><span class="ml-1">Like</span></div>
                                    <div class="like p-2 cursor"><i class="fa fa-commenting-o"></i><span class="ml-1">Reply</span></div>
                                </div>
                            </div>
                            
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
    document.getElementById("forum").style.borderBottomColor = "white";
    x.style.display = "none";
    y.style.display = "block";

}
function forumFunction() {
    var x = document.getElementById("forum-content");
    var y = document.getElementById("info-content");
    document.getElementById("info").style.borderBottomColor = "white";
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