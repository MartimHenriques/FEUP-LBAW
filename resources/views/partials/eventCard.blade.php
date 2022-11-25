<div class="eventCard" data-id="{{ $event->id }}">

  <a href="/events/{{ $event->id}}">
      <div class="event-info">
        <h2>{{ $event->title }}</h2>
        @if ($event->visibility)
          <h5>Public</h5>
        @else
          <h5>Private</h5>
        @endif
        <h5>Local: {{$event->local}}</h5>
        <h5>{{$event->start_date}}</h5>
      </div>
  </a>


@if ($event->visibility)
  <!-- Button trigger modal -->
    <button id="copyButton" onclick="copyLinkFeed({{$event->id}});">Share</button>
@endif

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
</div>

