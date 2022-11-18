<div class="eventCard" data-id="{{ $event->id }}">
    <div class="event-card">
      <div class="event-info">
        <h2>{{ $event->title }}</h2>
        @if ($event->visibility)
          <h5>Public</h5>
        @else
          <h5>Private</h5>
        @endif
        <h5></h5>
        <h5>Local: {{$event->local}}</h5>
        <h5>{{$event->start_date}}</h5>
      </div>
    </div>
</div>