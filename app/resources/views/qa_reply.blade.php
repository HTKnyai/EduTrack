<li class="list-group-item">
    <strong>
        @if($reply->anonymize) 匿名 @else {{ $reply->user->name }} @endif
    </strong>:
    {{ $reply->contents }}
    <span class="text-muted">（{{ $reply->created_at }}）</span>

    @if($reply->allReplies->count() > 0)
        <ul class="list-group mt-2">
            @foreach($reply->allReplies as $nestedReply)
                @include('qa_reply', ['reply' => $nestedReply])
            @endforeach
        </ul>
    @endif
</li>