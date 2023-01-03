@extends ('layouts.app')

@section('content')
<div class="formbg-outer">
    <div class="formbg">
        <div class="formbg-inner" style="padding:48px;">           
            <h3>The user <strong>{{$user->username}}</strong> just reported {{$event->title}}</h3>
            <form action="/report/{{$not->id}}/deal" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="field">
                    <label for="reporter">Reporter:</label>
                    <input name="reporter" type="text" placeholder="Reporter" value="{{$user->username}}" disabled>
                </div>
                <div class="field">
                    <label for="date">Date:</label>
                    <input name="date" type="text" placeholder="No description provided" value="{{$report->date}}" disabled>
                </div>
                <div class="field">
                    <label for="description">Description:</label>
                    <input name="description" type="text" placeholder="description" value="{{$report->motive}}" disabled>
                </div>
                <div class="field">
                    <label for="event">Event:</label>
                    <input name="event" type="text" placeholder="event" value="{{$event->title}}" disabled>
                </div>
                <div class="field">
                    <label for="state">State:</label>
                    <input name="state" type="text" placeholder="State" value="{{$report->state}}" disabled>
                </div>
                @if (!$not->read)
                    <div class="col-md-12 text-center">
                        <button type="submit" name="action" value="ignore" class="btn btn-success">Ignore</button>
                        <button type="submit" name="action" value="block" class="btn btn-outline-danger">Block Event</button>
                    </div>
                @else
                    <div class="col-md-12 text-center">
                        @if($report->state == 'Rejected')
                            <button type="submit" name="action" value="ignore" class="btn btn-secondary" disabled>Ignored</button>
                        @else
                            <button type="submit" name="action" value="block" class="btn btn-secondary" disabled>Blocked</button>
                        @endif
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection