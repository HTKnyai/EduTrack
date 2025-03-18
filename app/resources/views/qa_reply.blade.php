<li class="list-group-item">
    <strong>
        @if($reply->anonymize) 匿名 @else {{ $reply->user->name }} @endif
    </strong>:
    {{ $reply->contents }}
    <span class="text-muted">（{{ $reply->created_at->format('Y-m-d H:i') }}）</span>

    <!-- 編集・削除ボタン（自分の回答のみ） -->
    @if(auth()->id() == $reply->user_id)
        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editQaModal{{ $reply->id }}">編集</button>
        <form action="{{ route('qas.destroy', $reply->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('本当に削除しますか？')">削除</button>
        </form>
    @endif

    <!-- 編集用モーダル -->
    <div class="modal fade" id="editQaModal{{ $reply->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">回答を編集</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('qas.update', $reply->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">内容</label>
                            <textarea name="contents" class="form-control" required>{{ $reply->contents }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-warning">更新</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--ネスト回答 -->
    @if($reply->replies->count() > 0)
        <ul class="list-group mt-2">
            @foreach($reply->replies as $nestedReply)
                @include('qa_reply', ['reply' => $nestedReply])
            @endforeach
        </ul>
    @endif
</li>