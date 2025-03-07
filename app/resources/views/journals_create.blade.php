@extends('layouts.app')

@section('title', '学習ジャーナル登録')

@section('content')
    <h1>学習ジャーナルを追加</h1>
    <form action="/journals/store" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">目標</label>
            <input type="text" name="goals" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">学習内容</label>
            <input type="text" name="learnings" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">疑問点</label>
            <input type="text" name="questions" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">登録</button>
    </form>
@endsection