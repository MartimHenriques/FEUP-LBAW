@extends ('layouts.app')

@section('title', 'AdminUsers')

@section('content')

<h1>Admin Page</h1>
<h3>Users</h3>

<table class="table table-striped">
    @foreach($users as $user)
        <tr>
            <td>{{$user->username}}</td> 
            <td><a class="button" href="{{route('deleteUser',['id'=>$user->id])}}">Delete User</a></td>
            <td><a id="block" type='button' class='button' style="{{ ($user->is_blocked) ? 'background-color: CornflowerBlue' : '' }}" href="/{{(!$user->is_blocked) ? 'blockUser' : 'unblockUser'}}/{{$user->id}}">
                    @if(!$user->is_blocked)
                        Block
                    @else
                        Unblock
                    @endif
                </a></td>
        </tr>
    @endforeach
</table>
<div class="text-center">
    {!! $users->links(); !!}
</div>
@endsection