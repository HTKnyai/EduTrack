@extends('layouts.app')

@section('content')
<div class="container">
    <h2>学習ジャーナル</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ユーザー名</th>
                <th>開始時間</th>
                <th>終了時間</th>
                <th>学習時間</th>
                <th>学習目標</th>
                <th>学習内容</th>
                <th>質問</th>
            </tr>
        </thead>
        <tbody>
            @foreach($journals as $journal)
            <tr>
                <td>{{ $journal->user->name }}</td>
                <td>{{ $journal->start_time }}</td>
                <td>{{ $journal->end_time }}</td>
                <td>{{ $journal->duration }} 秒</td>
                <td>{{ $journal->goals }}</td>
                <td>{{ $journal->learnings }}</td>
                <td>{{ $journal->questions }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection