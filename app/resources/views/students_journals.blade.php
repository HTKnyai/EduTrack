@extends('layouts.app')

@section('title', '生徒の学習ジャーナル')

@section('content')

@php
    use Carbon\Carbon;
@endphp

<!--編集・削除成功時のアラート-->
@if (session('success'))
    <div class="alert alert-warning" id="successMessage">
        {{ session('success') }}
    </div>

    <script>
        // メッセージをフェードアウト
        setTimeout(function() {
            let message = document.getElementById('successMessage');
            if (message) {
                message.style.transition = "opacity 0.5s ease-out"; // フェードアウト
                message.style.opacity = "0";
                setTimeout(() => message.remove(), 500); 
            }
        }, 2000); // ミリ秒
    </script>
@endif

<div class="container">
    <h2>{{ $student->name }} の学習ジャーナル</h2>

    <!-- 検索フォーム -->
    <form action="{{ route('students.journals', $student->id) }}" method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <label for="date">日付</label>
                <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-md-3">
                <label for="goal">学習目標</label>
                <input type="text" name="goal" id="goal" class="form-control" placeholder="目標で検索" value="{{ request('goal') }}">
            </div>
            <div class="col-md-3">
                <label for="learning">学習内容</label>
                <input type="text" name="learning" id="learning" class="form-control" placeholder="学習内容で検索" value="{{ request('learning') }}">
            </div>
            <div class="col-md-3">
                <label for="question">疑問</label>
                <input type="text" name="question" id="question" class="form-control" placeholder="疑問で検索" value="{{ request('question') }}">
            </div>
            <div class="col-md-3 mt-2">
                <button type="submit" class="btn btn-primary">検索</button>
            </div>
        </div>
    </form>

    <!-- 学習記録テーブル -->
    <table class="table">
        <thead>
            <tr>
                <th>開始時間</th>
                <th>終了時間</th>
                <th>学習時間（分）</th>
                <th>学習目標</th>
                <th>学習内容</th>
                <th>質問</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($journals as $journal)
            <tr>
                <td>{{ Carbon::parse($journal->start_time)->format('Y-m-d H:i:s') }}</td>
                <td>{{ Carbon::parse($journal->end_time)->format('Y-m-d H:i:s') }}</td>
                <td>{{ number_format($journal->duration / 60, 1) }} 分</td>
                <td>{{ $journal->goals }}</td>
                <td>{{ $journal->learnings }}</td>
                <td>{{ $journal->questions }}</td>
                <td>
                   <!-- 編集ボタン -->
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editJournalModal{{ $journal->id }}">編集</button>

                    <!-- 削除ボタン -->
                    <form action="{{ route('students.journals.destroy', $journal->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm mt-1" onclick="return confirm('本当に削除しますか？')">削除</button>
                    </form>
                </td>
            </tr>

            <!-- 編集用モーダル -->
            <div class="modal fade" id="editJournalModal{{ $journal->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">学習ジャーナルを編集</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('students.journals.update', $journal->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-3">
                                    <label class="form-label">開始時間</label>
                                    <input type="datetime-local" name="start_time" class="form-control" 
                                        value="{{ Carbon::parse($journal->start_time)->format('Y-m-d\TH:i') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">終了時間</label>
                                    <input type="datetime-local" name="end_time" class="form-control" 
                                        value="{{ Carbon::parse($journal->end_time)->format('Y-m-d\TH:i') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">学習目標</label>
                                    <input type="text" name="goals" class="form-control" value="{{ $journal->goals }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">学習内容</label>
                                    <textarea name="learnings" class="form-control" required>{{ $journal->learnings }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">疑問点</label>
                                    <textarea name="questions" class="form-control">{{ $journal->questions }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-warning">更新</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>

    <!-- ページネーション -->
    <div class="mt-3">
        {{ $journals->links() }}
    </div>
</div>
@endsection