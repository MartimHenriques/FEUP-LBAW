
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
<button>Showing up</button>
<button onclick="infoFunction()">Info</button>
<button onclick="forumFunction()">Forum</button>

@if ($event->visibility)

<!-- Button trigger modal -->
<button data-bs-toggle="modal" data-bs-target="#myModel" id="shareBtn" data-bs-placement="top" title="Click Me!">
        Share Event 
    </button>
  
  <!-- Modal -->
    <div class="modal fade" id="myModel" tabindex="-1" aria-labelledby="myModelLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModelLabel">Share Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    <p>Copy link</p>
                    <div class="field d-flex align-items-center justify-content-between">
                        <span class="fas fa-link text-center"></span>
                        <input type="text" value="http://127.0.0.1:8000//events/{{$event->id}}" id="copyText">
                        <button onclick="copyLink()" id="copyButton">Copy Link</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endif

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
</div>


<div id="forum-content" style="display: none">
    <form class="profile-post-form" method="post">
        <textarea class="form-control autogrow" placeholder="What's on your mind?" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 80px;"></textarea>
        <div class="form-options">
            <div class="post-type">
                <a href="#" class="tooltip-primary" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Upload a Picture">
                    <i class="entypo-camera"></i>
                </a>
                <a href="#" class="tooltip-primary" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Attach a file">
                    <i class="entypo-attach"></i>
                </a>
                <a href="#" class="tooltip-primary" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Check-in">
                    <i class="entypo-location"></i>
                </a>
            </div>
            <div class="post-submit">
                <button type="button" class="btn btn-primary">POST</button>
            </div>
        </div>
     </form>
    <!-- Forum List -->
    <div class="inner-main-body p-2 p-sm-3 collapse forum-content show">
        @foreach($messages as $message)
            @include('partials.message', ['message' => $message])
        @endforeach
    </div>
    <!-- /Forum List -->
</div>

<script>
function infoFunction() {
    var x = document.getElementById("forum-content");
    var y = document.getElementById("info-content");
    x.style.display = "none";
    y.style.display = "block";
}
function forumFunction() {
    var x = document.getElementById("forum-content");
    var y = document.getElementById("info-content");
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
if (php_var) {
    myModal.toggle();
}

</script>
@endsection