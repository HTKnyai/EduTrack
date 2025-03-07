@extends('layouts.app')

@section('title', 'Q&A（質問掲示板）')

@section('content')
<div class="container">
    <h2>Q&A（質問掲示板）</h2>

    <!-- 質問投稿ボタン -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#qaModal">投稿</button>

    <!-- 質問投稿モーダル -->
    <div class="modal fade" id="qaModal" tabindex="-1" aria-labelledby="qaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qaModalLabel">質問を投稿</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/qas/store" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">内容</label>
                            <textarea name="contents" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">新規/回答対象</label>
                            <select name="target_id" class="form-select">
                                <option value="0">新規質問</option>
                                @foreach($qas as $qa)
                                    <option value="{{ $qa->id }}">{{ $qa->contents }}</option>
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

    <!-- 質問一覧（ツリー形式） -->
    <div class="mt-4">
        <ul class="list-group">
            @foreach($qas->where('target_id', 0) as $qa)
                <li class="list-group-item">
                    <strong>
                        @if($qa->anonymize) 匿名 @else {{ $qa->user->name }} @endif
                    </strong>:
                    {{ $qa->contents }}
                    <span class="text-muted">（{{ $qa->created_at }}）</span>

                    <!-- 回答一覧（入れ子リスト） -->
                    @if($qa->replies->count() > 0)
                        <ul class="list-group mt-2">
                            @foreach($qa->replies as $reply)
                                <li class="list-group-item">
                                    <strong>
                                        @if($reply->anonymize) 匿名 @else {{ $reply->user->name }} @endif
                                    </strong>:
                                    {{ $reply->contents }}
                                    <span class="text-muted">（{{ $reply->created_at }}）</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>

<!-- Bootstrapのスクリプト -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
