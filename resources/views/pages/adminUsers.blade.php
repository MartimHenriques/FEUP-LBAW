@extends ('layouts.app')

@section('title', 'AdminUsers')

@section('content')

<h3>Users</h3>

<table class="table table-striped">
    <tr>
        <th>User</th>
        <th> </th>
        <th> </th>
        <th> </th>
      </tr>
    @foreach($users as $user)
        <tr>
            <td>
                @if (empty($user->picture)) 
                <img class="profile-picture" src="/../avatars/default.png" alt="Avatar">
                @else
                <img class="profile-picture" src="/../avatars/{{$user->picture}}" alt="Avatar">
                @endif
            </td>
            <td>{{$user->username}}</td> 
            <td>
                <button data-bs-toggle="modal" data-bs-target="#myModel_<?php echo $user['id']; ?>" id="shareBtn" data-bs-placement="top" title="Delete User" style="float:right;">
                    Delete
                </button>
                        
                <!-- Modal -->
                <div class="modal fade" id="myModel_<?php echo $user['id']; ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="myModelLabel">Delete User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                                <div>
                                    <h5>Warning</h5>
                                    <p>All interections of '<?php echo $user['username']; ?>' will be eliminated. The user's events will be canceled.</p>
                                </div>
                                <div class="field d-flex align-items-center justify-content-between">
                                    <button onclick="window.location='{{route('deleteUser',['id'=>$user->id])}}'" id="deleteUserButton">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
            <td><a id="block" class='button' style="{{ ($user->is_blocked) ? 'background-color: CornflowerBlue' : '' }}" href="/{{(!$user->is_blocked) ? 'block/user' : 'unblock/user'}}/{{$user->id}}">
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