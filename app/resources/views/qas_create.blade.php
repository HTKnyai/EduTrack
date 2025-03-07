<!--使用しない-->
@extends('layouts.app')

@section('title', 'Q&A投稿')

@section('content')
    <h1>質問を投稿</h1>
    <form action="/qas/store" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">質問内容</label>
            <input type="text" name="contents" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">投稿</button>
    </form>
@endsection