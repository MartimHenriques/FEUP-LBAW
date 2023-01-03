<div class="eventCard" data-id="{{ $event->id }}">

  <a href="/events/{{ $event->id}}/info">
    <img src="/../img_events/{{ $event->picture}}" alt="event picture" id="eventMiniPicture">
    <div class="event-info">
    <p id="title">{{ $event->title }}</p>
    <p id="local">{{$event->local}}</p>
    <p>{{$event->start_date}}</p>
    @if($event->is_canceled)
    <h3>Event canceled</h3>
    @endif
    </div>
  </a>


@if ($event->visibility && !$event->is_canceled)
  <!-- Button trigger modal -->
    <button id="{{$event->id}}" onclick="copyLinkFeed({{$event->id}});">Share</button>
@endif

</div>
<script>
  function copyLinkFeed(id){
    var btn = document.getElementById(id);
    btn.innerHTML = 'link copied';
    btn.style.backgroundColor = "green";

    navigator.clipboard.writeText(window.location.href + "/" + id + "/info");

    setTimeout(function(){
        btn = document.getElementById(id);
        btn.innerHTML = 'Share';
        btn.style.backgroundColor = "#9bb6fcf6";
    }, 1000);
  }
</script>
