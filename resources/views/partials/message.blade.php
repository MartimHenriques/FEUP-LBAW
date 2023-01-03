
    <div id="{{ ($message->parent != null) ? 'son' : 'parent' }}" class="d-flex flex-column comment-section" msg-id="{{ $message->id }}">
        <div class="bg-white" style="border-top-left-radius: 1em; border-top-right-radius: 1em;">
            <div class="d-flex flex-row user-info">
                <img class="profile-picture" src="/../avatars/{{$message->user->picture}}">
                <div class="d-flex flex-column justify-content-start ml-2">
                    <span class="d-block font-weight-bold" style="margin: 1em 0 0;">{{ $message->user->username }}</span>
                    <span class="date text-black-50">{{ $message->date->format('d/m/Y - H:i') }}</span>
                </div>
            </div>
            <div class="mt-2">
                <p class="comment-text">{{ $message->content }}</p>
            </div>
        </div>
        <div class="bg-white" style="border-bottom-left-radius: 1em; border-bottom-right-radius: 1em; position: relative;"  >
            <div class="d-flex flex-row fs-12">
                
                <div class="like p-2 cursor">
                    @if(Auth::check())
                        <span>{{ count($message->votes) }}</span>
                        @if($message->voted(Auth::user()))
                            <span id="like" data-id="{{ $message->id }}" class="bi bi-hand-thumbs-up-fill" style="margin: 0;"></span>

                        @else
                            <span id="like" data-id="{{ $message->id }}" class="bi bi-hand-thumbs-up" style="margin: 0;"></span>
                        @endif
                        @if($message->id_user == Auth::id())
                        <a id="editBtn" ><i class="bi bi-pencil-fill"></i></a>
                        <a id="deleteBtn" href="/api/event/comment/delete/{{$message->id}}"><i class="bi bi-trash-fill"></i></a>
                        @endif
                    @endif
                </div>
                
            </div>
        </div>
    </div>
    @foreach ($message->messages as $son)
        @include('partials.message', ['message' => $son])
    @endforeach
    @if($message->parent == NULL && !$event->is_canceled && Auth::check())
    <div id="reply">
        <input id="replyInput" type="text" name="reply" placeholder="Write a reply">
        <button id="submitReply" type="submit">
            post
        </button>
    </div>

    @endif