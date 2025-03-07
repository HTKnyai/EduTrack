@extends('layouts.app')

@section('title', '生徒管理')

@section('content')
<div class="container">
    <h2>生徒管理</h2>
    <table class="table">
        <thead>
            <tr>
                <th>生徒ID</th>
                <th>生徒名</th>
                <th>学習時間（平均）</th>
                <th>学習時間（前日）</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach($studentData as $student)
            <tr>
                <td>{{ $student['id'] }}</td>
                <td>
                    <a href="{{ route('students.journals', $student['id']) }}">
                        {{ $student['name'] }}
                    </a>
                </td>
                <td>{{ $student['averageDuration'] }}</td>
                <td>{{ $student['yesterdayDuration'] }}</td>
                <td>
                    <a href="{{ route('students.journals', $student['id']) }}" class="btn btn-info btn-sm">ジャーナルを見る</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection