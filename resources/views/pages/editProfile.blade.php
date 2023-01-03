@extends ('layouts.app')

 
@section('content')
<section style="background-color: #eee;
                padding: 20px;
                margin: 20px;">


<form action="/profile/{{$users->id}}/edit" method="POST" enctype="multipart/form-data">
    <div class="container-pic-editProfile">
        @if (empty($users->picture)) 
        <img class="profile-picture" src="/../avatars/default.png" alt="Avatar">
        @else
        <img class="profile-picture" src="/../avatars/{{$users->picture}}" alt="Avatar">
        @endif
        <h4><b>{{$users->username}}</b></h4>
        <div class ="change_picture">
            <div class="card-header">
                <h4><b>{{ __('Change Picture') }}</b></h4>
            </div>
                <input id="picture" type="file" name="picture" accept="image/png, image/gif, image/jpeg ,image/jpg" >
        </div>
    </div>

    <div class="container-password-editProfile">  
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h4><b>{{ __('Change Password') }}</b></h4></div>
                    
                        @csrf
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="oldPasswordInput" class="form-label">Old Password</label>
                                <input name="old_password" type="password" class="form-control @error('old_password') is-invalid @enderror" id="oldPasswordInput"
                                    placeholder="Old Password">
                                @error('old_password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="newPasswordInput" class="form-label">New Password</label>
                                <input name="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror" id="newPasswordInput"
                                    placeholder="New Password">
                                @error('new_password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="confirmNewPasswordInput" class="form-label">Confirm New Password</label>
                                <input name="new_password_confirmation" type="password" class="form-control" id="confirmNewPasswordInput"
                                    placeholder="Confirm New Password">
                            </div>

                        </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @elseif (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="save-edit-profile-button">
            <button>Save Changes</button>
    </div>
</form>



</section>
@endsection