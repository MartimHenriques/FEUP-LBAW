@extends ('layouts.app')

@section('content')
<div class="row m-0" style="padding-top:100px;padding-left:25%">
    <div class="col-md-10">
        <div class="row m-0">
            <div class="col-md-9">
                <form action="/invites/{{$not->id}}/deal" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mt-3 container text-center align-items-center">
                        <h3><?php echo $event->title; ?></h3>
                    </div>
                    <div class="card my-3">

                        <div class="card-body">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">Title: </span>
                                </div>
                                <input name="title" type="text" class="form-control" placeholder="Title" value="{{$event->title}}" disabled>
                            </div>
                            <div class="input-group mb-3">
                                <h5 class="text-center col-md-12">Description</h5>
                                <div class="input-group">
                                    <textarea name="description" class="form-control" aria-label="With textarea" rows="10" placeholder="Description" disabled>{{$event->description}}</textarea>
                                </div>
                            </div>
                            <div class="row m-0 col-md-12 ">
                                <div class="input-group mb-3 " style="width: 50%; padding-left: 0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"> Role: </span>
                                    </div>
                                </div>
                            </div>
                            @if (!$not->read)
                            <div class="col-md-12 text-center">
                                <button type="submit" name="action" value="accept" class="btn btn-success">Accept Invite</button>
                                <button type="submit" name="action" value="refuse" class="btn btn-outline-danger">Refuse Invite</button>
                            </div>
                            @else
                                <div class="col-md-12 text-center">
                                @if($invite->accepted)
                                    <button type="submit" name="action" value="accept" class="btn btn-success" disabled>Accepted Invite</button>
                                @else
                                    <button type="submit" name="action" value="refuse" class="btn btn-outline-danger" disabled>Refused Invite</button>
                                @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection