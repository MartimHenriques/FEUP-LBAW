@extends ('layouts.app')

@section('content')
<section style="background-color: #eee;">
   
    <div class="row">
    <div class="col-lg-4">
        <div class="card mb-4">
        <div class="card-body text-center">
            <img src="{{$users->picture}}" alt="avatar"
            class="rounded-circle img-fluid" style="width: 150px;">
            <h5 class="my-3">{{$users->username}}</h5>
        </div>
        </div>

    <div class="col-lg-8">
        <div class="card mb-4">
        <div class="card-body">
            <div class="row">
            <div class="col-sm-3">
                <p class="mb-0">Email</p>
            </div>
            <div class="col-sm-9">
                <p class="text-muted mb-0">{{$users->email}}</p>
            </div>
            </div>
            <div class="row">
            <div class="col-sm-3">
                <a class="button" href="{{ url('/home') }}"> Edit Profile </a>
            </div>
            </div>     
 </section>
@endsection

