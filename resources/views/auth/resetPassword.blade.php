@extends('layouts.app')

@section('content2')
<form method="POST" action="/recover_password">
        <div class="fs-2">Recover Password</div>
        <div class="text-muted fs-5">Please fill the form to reset your password!</div>
    
          <input type="hidden" value="{{ $token }}" name="token">

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
            
            <label for="inputPassword" class="form-label">New Password <span class="text-muted">*</span></label>
            <input type="password" class="form-control" id="inputPassword" name="password" required>

            <label for="inputPasswordConfirmation" class="form-label">New Password Confirmation <span class="text-muted">*</span></label>
            <input type="password" class="form-control" id="inputPasswordConfirmation" name="password_confirmation" required>

          <button type="submit" class="button" style="background-color:green">
              Recover
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
