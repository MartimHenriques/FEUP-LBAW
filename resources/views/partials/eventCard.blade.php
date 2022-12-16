<div class="eventCard" data-id="{{ $event->id }}">

  <a href="/events/{{ $event->id}}">
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
    var dummy = document.createElement('input'),
    text = window.location.href + "/" + id;
    document.body.appendChild(dummy);
    dummy.value = text;
    dummy.select();
    document.execCommand('copy');
    document.body.removeChild(dummy);

    // Alert the copied text
    alert("Copied the text: " + dummy.value); 
  }
</script>
