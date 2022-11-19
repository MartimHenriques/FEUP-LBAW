
@extends ('layouts.app')

@section('title', 'Event')

@section('content')

<h1>{{ $event->title }}</h1>

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

</script>
@endsection