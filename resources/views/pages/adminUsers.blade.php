@extends ('layouts.app')

@section('title', 'AdminUsers')

@section('content')

<h1>Admin Page</h1>
<h3>Manage Users</h3>
<table class="table table-striped">
<th>Users</th>

    @foreach($users as $user)
        @if(!$user->is_admin)
            <tr>
                <td>{{$user->username}}</td> <td><a class="button" href="{{route('deleteUser',['id'=>$user->id])}}">Delete User</a></td>
            </tr>
        @endif
    @endforeach

</table>
@endsection