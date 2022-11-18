<div class="eventCard" data-id="{{ $event->id }}">
    <div class="event-card">
      <div class="event-info">
        <h2>{{ $event->title }}</h2>
        <h5>{{$event->visibility}}</h5>
        <h5>Local: {{$event->local}}</h5>
        <h5>{{$event->start_date}}</h5>
      </div>
    </div>
</div>