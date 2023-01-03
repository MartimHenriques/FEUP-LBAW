@extends('layouts.app')

@section('title', 'Editar evento')

@section('content')
<div class="formbg-outer">
  <div class="formbg">
    <div class="formbg-inner" style="padding: 48px">
      <span style="padding-bottom: 15px; font-size: 50px;">Edit Event</span>
      <form id="stripe-login" action="/editEvent/{{$id}}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="field" style="padding-bottom: 24px">
          <label>Title</label>
          <input class="title" type="text" name="title" value="{{ $event->title }}" placeholder="{{ $event->title }}" required autofocus>
        </div>
        @if ($errors->has('title'))
        <span class="error">
          {{ $errors->first('title') }}
        </span>
        @endif

        <div class="field" style="padding-bottom: 24px">
          <label for="description">Description</label>
          <input id="description" type="text" name="description" value="{{ $event->description }}" placeholder="{{ $event->description }}" autofocus>
        </div>
        @if ($errors->has('description'))
        <span class="error">
          {{ $errors->first('description') }}
        </span>
        @endif

        <div class="field" style="padding-bottom: 24px">
          <label>Visibility</label>
          <select name="visibility" required>
              @if ($event->visibility == "1")
              <option value="1" selected> Public </option>
              <option value="0"> Private </option>
              @else
              <option value="1"> Public </option>
              <option value="0" selected> Private </option>
              @endif
          </select>
        </div>
        @if ($errors->has('visibility'))
        <span class="error">
          {{ $errors->first('visibility') }}
        </span>
        @endif

        <div class="field" style="padding-bottom: 24px">
          <label for="picture">Picture</label>
          <input id="picture" type="file" name="picture" value="{{ $event->picture }}" placeholder="{{ $event->picture }}" accept="image/png, image/gif, image/jpeg ,image/jpg,gif" autofocus>
        </div>
        @if ($errors->has('picture'))
        <span class="error">
          {{ $errors->first('picture') }}
        </span>
        @endif

        <div class="field" style="padding-bottom: 24px">
          <label>Local</label>
          <input class="local" type="text" name="local" value="{{ $event->local }}" placeholder="{{ $event->local }}" required autofocus>
        </div>
        @if ($errors->has('local'))
        <span class="error">
          {{ $errors->first('local') }}
        </span>
        @endif

        <div class="field" style="padding-bottom: 24px">
          <label for="start_date">Start date</label>
          <input id="start_date" type="datetime-local" name="start_date" value="{{ date('Y-m-d h:m',strtotime($event->start_date)) }}" required autofocus>
        </div>
        @if ($errors->has('start_date'))
        <span class="error">
          {{ $errors->first('start_date') }}
        </span>
        @endif

        <div class="field" style="padding-bottom: 24px">
          <label for="final_date">Final date</label>
          <input id="final_date" type="datetime-local" name="final_date" value="{{ date('Y-m-d h:m',strtotime($event->final_date)) }}" required autofocus>
        </div>
        @if ($errors->has('final_date'))
        <span class="error">
          {{ $errors->first('final_date') }}
        </span>
        @endif

        <div class="field" style="padding-bottom: 24px">
          <input type="submit" name="submit" value="Edit Event">
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
