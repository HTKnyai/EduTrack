@extends('layouts.app')

@section('title', '生徒の学習ジャーナル')

@section('content')
<div class="container">
    <h2>{{ $student->name }} の学習ジャーナル</h2>
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
</div>
@endsection