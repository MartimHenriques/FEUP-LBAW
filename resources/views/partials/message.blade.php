@if($message->parent == NULL)
<div class="message" msg-id="{{ $message->id }}">
    @endif
    <div id="{{ ($message->parent != null) ? 'son' : 'parent' }}" class="d-flex flex-column comment-section">
        <div class="bg-white" style="border-top-left-radius: 1em; border-top-right-radius: 1em;">
            <div class="d-flex flex-row user-info"><img class="profile-picture" src="/../avatars/{{$setMessage[$message->id]->picture}}">
                <div class="d-flex flex-column justify-content-start ml-2">
                    <span class="d-block font-weight-bold" style="margin: 1em 0 0;">{{ $setMessage[$message->id]->username }}</span>
                    <span class="date text-black-50">{{ $message->date }}</span>
                </div>
            </div>
            <div class="mt-2">
                <p class="comment-text">{{ $message->content }}</p>
            </div>
        </div>
        <div class="bg-white" style="border-bottom-left-radius: 1em; border-bottom-right-radius: 1em; position: relative;"  >
            <div class="d-flex flex-row fs-12">
                
                <div class="like p-2 cursor">
                    <span>{{ count($message->votes) }}</span>
                    @if($message->voted(Auth::user()))
                        <span id="like" data-id="{{ $message->id }}" class="bi bi-hand-thumbs-up-fill" style="margin: 0;"></span>

                    @else
                        <span id="like" data-id="{{ $message->id }}" class="bi bi-hand-thumbs-up" style="margin: 0;"></span>
                    @endif
                    @if($message->id_user == Auth::id())
                    <a id="editBtn" href="/editMessage/{{$message->id}}"><i class="bi bi-pencil-fill"></i></a>
                    <a id="deleteBtn" href="/deleteMessage/{{$message->id}}"><i class="bi bi-trash-fill"></i></a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @foreach ($message->messages as $son)
        @include('partials.message', ['message' => $son])
    @endforeach
    @if($message->parent == NULL)
    <div id="reply">
        <input id="replyInput" type="text" name="reply" placeholder="Write reply">
        <button id="submitReply" type="submit">
            post
        </button>
    </div>

</div>
@endif