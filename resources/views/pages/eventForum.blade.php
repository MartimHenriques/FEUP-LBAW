
@extends ('layouts.app')

@section('title', 'Event')

@section('content')

@include('partials.eventHeader')
@yield('eventHeader')

<div id="forum-content">
    <!-- Forum List -->
    <div class=" p-2 p-sm-3 collapse forum-content show">
            <div class="container mt-5">
                <div class="d-flex justify-content-center row" style="margin: 0;">
                    <div class="col-md-8">
                        <div id="postMessage">
                            <input id="messageInput" type="text" name="content" placeholder="Write a comment">
                            <button id="submitComment" type="submit">
                                post
                            </button>
                        </div>
                        @foreach($messages as $message)
                        @if($message->parent == NULL)
                            <div class="message" msg-id="{{ $message->id }}">
                            @include('partials.message', ['message' => $message])
                            </div>
                        @endif
                        
                        @endforeach
                    </div>
                </div>
            </div>
    </div>
</div>
@endsection