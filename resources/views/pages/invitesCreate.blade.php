@extends('layouts.app')

@section('title', 'Invite')

@section('content')
<div class="formbg-outer">
  <div class="formbg">
    <div class="formbg-inner" style="padding: 48px">
      <span style="padding-bottom: 15px">Invite User</span>
      <form action="/event/{{$event->id}}/invite" method="POST">
        @csrf
        <div class="field" style="padding-bottom: 24px">
          <label for="username">Username</label>
          <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus>
        </div>
        @if ($errors->has('username'))
        <span class="error">
          {{ $errors->first('username') }}
        </span>
        @endif
        <div class="field" style="padding-bottom: 24px">
          <label for="user_role">Role</label>
          <select name="user_role" required autofocus>
            <option value='ATTENDEE' selected>Attendee</option>
            <option value='ORGANIZER'>Organizer</option>
          </select>
        </div>
        @if ($errors->has('user_role'))
        <span class="error">
          {{ $errors->first('user_role') }}
        </span>
        @endif

        <div class="field" style="padding-bottom: 24px">
          <input type="submit" name="submit" value="Send Invite">
        </div>
      </form>
    </div>
  </div>
</div>
@endsection