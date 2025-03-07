@extends('layouts.app')

@section('title', '生徒の学習ジャーナル')

@section('content')
<div class="container">
    <h2>{{ $student->name }} の学習ジャーナル</h2>

    <!-- 検索フォーム -->
    <form action="{{ route('students.journals', $student->id) }}" method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="goal" class="form-control" placeholder="目標で検索" value="{{ request('goal') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="learning" class="form-control" placeholder="学習内容で検索" value="{{ request('learning') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="question" class="form-control" placeholder="疑問で検索" value="{{ request('question') }}">
            </div>
            <div class="col-md-3 mt-2">
                <button type="submit" class="btn btn-primary">検索</button>
            </div>
        </div>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>開始時間</th>
                <th>終了時間</th>
                <th>学習時間（分）</th>
                <th>学習目標</th>
                <th>学習内容</th>
                <th>質問</th>
            </tr>
        </thead>
        <tbody>
            @foreach($journals as $journal)
            <tr>
                <td>{{ $journal->start_time }}</td>
                <td>{{ $journal->end_time }}</td>
                <td>{{ round($journal->duration / 60, 1) }}</td>
                <td>{{ $journal->goals }}</td>
                <td>{{ $journal->learnings }}</td>
                <td>{{ $journal->questions }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- ページネーション -->
    <div class="mt-3">
        {{ $journals->links() }}
    </div>
</div>
@endsection