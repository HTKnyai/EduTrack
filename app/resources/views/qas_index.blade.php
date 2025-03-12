@extends('layouts.app')

@section('title', 'Q&A（質問掲示板）')

@section('content')
<div class="container">
    <h2>Q&A（質問掲示板）</h2>

    <!-- 🔹 質問投稿ボタン -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#qaModal">投稿</button>

    <!-- 🔍 検索フォーム -->
    <form action="{{ route('qas_index') }}" method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <label>開始日:</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label>終了日:</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3">
                <label>キーワード検索:</label>
                <input type="text" name="keyword" class="form-control" placeholder="例: 計算ミス" value="{{ request('keyword') }}">
            </div>
            <div class="col-md-3">
                <label>投稿者:</label>
                <input type="text" name="user" class="form-control" placeholder="投稿者名" value="{{ request('user') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">検索</button>
            </div>
        </div>
    </form>

    <!-- ✏ 質問投稿モーダル -->
    <div class="modal fade" id="qaModal" tabindex="-1" aria-labelledby="qaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qaModalLabel">質問を投稿</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('qas.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">内容</label>
                            <textarea name="contents" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">新規/回答対象</label>
                            <input type="text" id="qaSearch" class="form-control" placeholder="質問を検索">
                            <select name="target_id" class="form-select mt-2" id="qaSelect">
                                <option value="0">新規質問</option>
                                @foreach($qas as $qa)
                                    <option value="{{ $qa->id }}">{{ Str::limit($qa->contents, 30) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="anonymize" name="anonymize" value="1">
                            <label class="form-check-label" for="anonymize">匿名で投稿</label>
                        </div>
                        <button type="submit" class="btn btn-primary">投稿</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 📄 質問一覧（ツリー形式） -->
    <div class="mt-4">
        <ul class="list-group">
            @foreach($qas->where('target_id', 0) as $qa)
                <li class="list-group-item">
                    <strong>
                        @if($qa->anonymize) 匿名 @else {{ $qa->user->name }} @endif
                    </strong>:
                    {{ $qa->contents }}
                    <span class="text-muted">（{{ $qa->created_at->format('Y-m-d H:i') }}）</span>

                    <!-- ✏ 編集・削除ボタン（自分の投稿のみ） -->
                    @if(auth()->id() == $qa->user_id)
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editQaModal{{ $qa->id }}">編集</button>
                        <form action="{{ route('qas.destroy', $qa->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('本当に削除しますか？')">削除</button>
                        </form>
                    @endif

                    <!-- 編集用モーダル -->
                    <div class="modal fade" id="editQaModal{{ $qa->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">質問を編集</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('qas.update', $qa->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-3">
                                            <label class="form-label">内容</label>
                                            <textarea name="contents" class="form-control" required>{{ $qa->contents }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-warning">更新</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 📌 回答一覧（入れ子リスト） -->
                    @if($qa->replies->count() > 0)
                        <ul class="list-group mt-2">
                            @foreach($qa->replies as $reply)
                                @include('qa_reply', ['reply' => $reply])
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    <!-- 📌 ページネーション -->
    <div class="d-flex justify-content-center mt-3">
        {{ $qas->appends(request()->query())->links() }}
    </div>
</div>

<!-- ✅ 検索用スクリプト -->
<script>
document.getElementById('qaSearch').addEventListener('input', function() {
    let filter = this.value.toLowerCase();
    let options = document.getElementById('qaSelect').options;

    for (let i = 0; i < options.length; i++) {
        let text = options[i].text.toLowerCase();
        options[i].style.display = text.includes(filter) ? "" : "none";
    }
});
</script>

<!-- Bootstrapのスクリプト -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection