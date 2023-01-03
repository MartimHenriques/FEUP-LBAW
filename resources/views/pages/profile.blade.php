@extends ('layouts.app')

@section('content')
<section style="background-color: #eee; padding: 20px; margin: 20px;">

    <div class="card-edit">
        @if (empty($users->picture)) 
        <img class="profile-picture" src="/../avatars/default.png" alt="Avatar">
        @else
        <img class="profile-picture" src="/../avatars/{{$users->picture}}" alt="Avatar">
        @endif
        <div class="container">
            <h4><b>{{$users->username}}</b></h4>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-3">
                        <p class="mb-0">Email:</p>
                    </div>
                    <div class="col-sm-9" >
                        <p class="text-muted mb-0">{{$users->email}}</p>
                    </div>
                </div>
            <div class="editProfile">
                <div class="col-sm-3">
                    <a class="button" href="/profile/{{$users->id}}/edit"> Edit Profile </a> 
                </div>
            </div> 
        </div>  
        
    </div> 
    <a class="button" href="{{ url('/logout') }}"> Logout </a>

        <!-- Button trigger modal -->
    <button type="button" data-toggle="modal" data-target="#exampleModalCenter" style="background-color: #DC143C">
    Delete
    </button>

    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <div class="modal-body">
            Careful! You are about to delete your account. Unless other attendees are also organizers, all of your events will be canceled. Are you sure?
           </div>
        </div>
        <div class="modal-footer">
            <a class="button" data-dismiss="modal">Close</a>
            <a class="button" href="/profile/{{$users->id}}/delete" style="background-color:#DC143C;"> Delete</a>
        </div>
        </div>
    </div>
    </div>
    
</section>
@endsection

