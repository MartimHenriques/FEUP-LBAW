@extends ('layouts.app')

@section('content')
<h3>Home</h3>
@if(Auth::check() && Auth::user()->is_admin)
<h5>Manage Users</h5>
@foreach($users as $user)
    @if(!$user->is_admin)
        <p>{{$user->username}} <a type='button' class='button' href="{{route('deleteUser',['id'=>$user->id])}}">Delete User</a></p>
    @endif
@endforeach
@endif
@endsection