<div class="eventCard" data-id="{{ $event->id }}">

  <a href="/events/{{ $event->id}}/info">
    <img src="/../img_events/{{ $event->picture}}" alt="event picture" id="eventMiniPicture">
    <div class="event-info">
    <p id="title">{{ $event->title }}</p>
    <p id="local">{{$event->local}}</p>
    <p>{{$event->start_date}}</p>
    </div>
  </a>


@if ($event->visibility)
  <!-- Button trigger modal -->
    <button id="copyButton" onclick="copyLinkFeed({{$event->id}});">Share</button>
@endif


</div>
<script>
  function copyLinkFeed(id){
    var dummy = document.getElementById("input");
    navigator.clipboard.writeText(window.location.href + "/" + id);

    alert("Link Copied ✔️");
  }
</script>
