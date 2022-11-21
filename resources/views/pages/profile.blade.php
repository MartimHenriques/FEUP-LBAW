@extends ('layouts.app')

@section('content')
<section style="background-color: #eee;
                padding: 20px;
                margin: 20px;">

    <div class="card-edit">
        @if (empty($users->picture)) 
        <img src="/../avatars/default.png" alt="Avatar">
        @else
        <img src="/../avatars/{{$users->picture}}" alt="Avatar">
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
                <a class="button" href="{{ url('/profile/editProfile') }}"> Edit Profile </a> 
            </div>
            </div>     
 </section>
@endsection

