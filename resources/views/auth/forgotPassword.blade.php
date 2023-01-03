@extends('layouts.app')

@section('content2')
<form method="POST" action="/forgot_password">
    <div class="fs-2">Recover Password</div>
    <div class="text-muted fs-5">Please provide us your email so we can send you a recovery link!</div>
      
    <label for="email">E-mail</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
    @if ($errors->has('email'))
        <span class="error">
          {{ $errors->first('email') }}
        </span>
    @endif

    @csrf

    @if (Session::has('message'))
      <div class="alert alert-success mb-3" role="alert">
        {{ Session::get('message') }}
      </div>
    @endif

    @if ($errors->first())
      <div class="alert alert-danger mb-3" role="alert">
        {{ $errors->first() }}
      </div>
    @endif
    
    <button type="submit" class="button" style="background-color:green">
              Submit
            </button>
    <a href="{{ URL::previous() }}" role="button" class="button">Cancel</a>
          
  </form>
@endsection