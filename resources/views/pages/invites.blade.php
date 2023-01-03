@extends ('layouts.app')

@section('content')
<div class="formbg-outer">
    <div class="formbg">
        <div class="formbg-inner" style="padding:48px;">           
            <h3>You got invited to <strong>@if ($invite->to_attend) attend @else organize @endif</strong> a private event!</h3>
            <form action="/invites/{{$not->id}}/deal" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="field">
                    <label>Title:</label>
                    <input name="title" type="text" placeholder="Title" value="{{$event->title}}" disabled>
                </div>
                <div class="field">
                    <label for="description">Description:</label>
                    <input name="description" type="text" placeholder="No description provided" value="{{$event->description}}" disabled>
                </div>
                <div class="field">
                    <label>Local:</label>
                    <input name="local" type="text" placeholder="Local" value="{{$event->local}}" disabled>
                </div>
                <div class="field">
                    <label for="start_date">Start date:</label>
                    <input name="start_date" type="text" placeholder="Start date" value="{{$event->start_date}}" disabled>
                </div>
                <div class="field">
                    <label for="final_date">Final date:</label>
                    <input name="final_date" type="text" placeholder="final date" value="{{$event->final_date}}" disabled>
                </div>
                @if (!$not->read)
                    <div class="col-md-12 text-center">
                        <button type="submit" name="action" value="accept" class="btn btn-success">Accept Invite</button>
                        <button type="submit" name="action" value="refuse" class="btn btn-outline-danger">Refuse Invite</button>
                    </div>
                @else
                    <div class="col-md-12 text-center">
                        @if($invite->accepted)
                            <button type="submit" name="action" value="accept" class="btn btn-secondary" disabled>Accepted Invite</button>
                        @else
                            <button type="submit" name="action" value="refuse" class="btn btn-secondary" disabled>Refused Invite</button>
                        @endif
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection