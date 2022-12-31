<div id="son" class="d-flex flex-column comment-section">
    <div class="bg-white" style="border-top-left-radius: 1em; border-top-right-radius: 1em;">
        <div class="d-flex flex-row user-info"><img class="profile-picture" src="/../avatars/{{$setMessage[$son->id]->picture}}">
            <div class="d-flex flex-column justify-content-start ml-2"><span class="d-block font-weight-bold" style="margin: 1em 0 0;">{{ $setMessage[$son->id]->username }}</span><span class="date text-black-50">{{ $message->date }}</span></div>
        </div>
        <div class="mt-2">
            <p class="comment-text">{{ $son->content }}</p>
        </div>
    </div>
    <div class="bg-white" style="border-bottom-left-radius: 1em; border-bottom-right-radius: 1em; position: relative;"  >
        <div class="d-flex flex-row fs-12">
            
            <div class="like p-2 cursor">
                <span>{{ count($son->votes) }}</span>
                @if($son->voted(Auth::user()))
                    <span id="like" data-id="{{ $son->id }}" class="bi bi-hand-thumbs-up-fill" style="margin: 0;"></span>

                @else
                    <span id="like" data-id="{{ $son->id }}" class="bi bi-hand-thumbs-up" style="margin: 0;"></span>
                @endif
                @if($son->id_user == Auth::id())
                <a id="editBtn" href="/editMessage/{{$son->id}}"><i class="bi bi-pencil-fill"></i></a>
                <a id="deleteBtn" href="/deleteMessage/{{$son->id}}"><i class="bi bi-trash-fill"></i></a>
                @endif
            </div>
        </div>
    </div>
</div>