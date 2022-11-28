@extends ('layouts.app')

@section('title', 'Admin')

@section('content')

<h1>Admin Page</h1>
<h3>Manage Users</h3>
<table class="table table-striped">
<th>Users</th>

    @foreach($users as $user)
        @if(!$user->is_admin)
            <td>{{$user->username}}</td> <td><a href="{{route('deleteUser',['id'=>$user->id])}}">Delete User</a></td> 
        @endif
    @endforeach

</table>
@endsection