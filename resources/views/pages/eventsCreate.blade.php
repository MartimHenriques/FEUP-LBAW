@extends('layouts.app')

@section('title', 'Novo evento')

@section('content')
<div class="formbg-outer">
  <div class="formbg">
    <div class="formbg-inner" style="padding: 48px">
      <h5 style="padding-bottom: 0.5em">New Event</h5>
      <form id="stripe-login" action="/events_create" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="field" style="padding-bottom: 24px">
          <label for="title">Title *</label>
          <input id="title" type="text" name="title" value="{{ old('title') }}" required autofocus>
        </div>
        @if ($errors->has('title'))
        <span class="error">
          {{ $errors->first('title') }}
        </span>
        @endif

        <div class="field" style="padding-bottom: 24px">
          <label for="description">Description</label>
          <input id="description" type="text" name="description" value="{{ old('description') }}">
        </div>
        @if ($errors->has('description'))
        <span class="error">
          {{ $errors->first('description') }}
        </span>
        @endif

        <div class="field" style="padding-bottom: 25px;">
          <label for="visibility">Visibility *</label>
          <select name="visibility" required>
              <option value="1">Public</option>
              <option value="0">Private</option>
          </select>
        </div>
        @if ($errors->has('visibility'))
        <span class="error">
          {{ $errors->first('visibility') }}
        </span>
        @endif

        <div class="field" style="padding-bottom: 24px">
          <label for="picture">Picture</label> 
          <input id="picture" type="file" name="picture" accept="image/png, image/gif, image/jpeg ,image/jpg">
        </div>
        @if ($errors->has('picture'))
        <span class="error">
          {{ $errors->first('picture') }}
        </span>
        @endif

        <div class="field" style="padding-bottom: 24px">
          <label for="local">Location *</label>
          <input id="local" type="text" name="local" value="{{ old('local') }}" required autofocus>
        </div>
        @if ($errors->has('local'))
        <span class="error">
          {{ $errors->first('local') }}
        </span>
        @endif

        <div class="field" style="padding-bottom: 24px">
          <label for="start_date">Start date *</label>
          <input id="start_date" type="datetime-local" name="start_date" value="{{ old('start_date') }}" required autofocus>
        </div>
        @if ($errors->has('start_date'))
        <span class="error">
          {{ $errors->first('start_date') }}
        </span>
        @endif

        <div class="field" style="padding-bottom: 24px">
          <label for="final_date">Final date *</label>
          <input id="final_date" type="datetime-local" name="final_date" value="{{ old('final_date') }}" required autofocus>
        </div>
        @if ($errors->has('final_date'))
        <span class="error">
          {{ $errors->first('final_date') }}
        </span>
        @endif

        <div class="field" style="padding-bottom: 24px">
          <input id="submit" type="submit" name="submit" value="create event" >

        </div>

      </form>
    </div>
  </div>
</div>
@endsection
