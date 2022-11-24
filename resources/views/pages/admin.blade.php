@extends ('layouts.app')

@section('title', 'Admin')

@section('content')

<h1>Admin Page</h1>
<h3>Manage Users</h3>
<div>
@foreach($users as $user)
    @if(!$user->is_admin)
        <p>{{$user->username}} <a type='button' class='button' href="{{route('deleteUser',['id'=>$user->id])}}">Delete User</a></p>
    @endif
@endforeach
</div>
@endsection