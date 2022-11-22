
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
                    <div class="field d-flex align-items-center justify-content-between">
                        <button onclick="copyLink()" id="copyButton">Copy Link</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endif
<br>
<button onclick="infoFunction()">Info</button>
<button onclick="forumFunction()">Forum</button>
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
        @if(count($messages)<1)
            <p>There aren't messages yet</p>
        @endif
        @foreach($messages as $message)
            <div class="container mt-5">
                <div class="d-flex justify-content-center row">
                    <div class="col-md-8">
                        <div class="d-flex flex-column comment-section">
                            <div class="bg-white p-2">
                                <div class="d-flex flex-row user-info"><img class="rounded-circle" src="/../avatars/{{$setMessage[$message->id]->picture}}">
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
                    </div>
                </div>
            </div>

            
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