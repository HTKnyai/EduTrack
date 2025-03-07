@extends('layouts.app')

@section('title', '教材アップロード')

@section('content')
    <h1>教材をアップロード</h1>
    <form action="/materials/store" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">タイトル</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">ファイルパス</label>
            <input type="text" name="file_path" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">登録</button>
    </form>
@endsection