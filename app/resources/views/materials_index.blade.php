@extends('layouts.app')

@section('content')
<div class="container">
    <h2>教材一覧</h2>

    <!-- 教材アップロードボタン -->
    @if(auth()->user()->role == 1)  <!-- 1: 教師 -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#materialModal">教材をアップロード</button>

        <!-- 教材アップロードモーダル -->
        <div class="modal fade" id="materialModal" tabindex="-1" aria-labelledby="materialModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="materialModalLabel">教材をアップロード</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="/materials/store" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">タイトル</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ファイル</label>
                                <input type="file" name="file" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">登録</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- 教材一覧テーブル -->
    <table class="table">
        <thead>
            <tr>
                <th>タイトル</th>
                <th>アップロード者</th>
                <th>ダウンロード数</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($materials as $material)
            <tr>
                <td>{{ $material->title }}</td>
                <td>{{ $material->teacher->name }}</td>
                <td>{{ $material->dls }}</td>
                <td>
                    <a href="{{ asset($material->file_path) }}" download class="btn btn-primary">ダウンロード</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Bootstrapのスクリプト -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection