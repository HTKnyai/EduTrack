@extends('layouts.app')

@section('title', '生徒管理')

@section('content')
<div class="container">
    <h2>生徒管理</h2>

    <!-- 検索フォーム -->
    <form action="{{ route('students.index') }}" method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="生徒名で検索" value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">検索</button>
        </div>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>生徒ID</th>
                <th>生徒名</th>
                <th>学習時間（平均）</th>
                <th>学習時間（前日）</th>
                <th>前日の学習目標</th>
                <th>前日の学習内容</th>
                <th>前日の疑問</th>
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
                <td>{{ $student['yesterdayGoals'] }}</td>
                <td>{{ $student['yesterdayLearnings'] }}</td>
                <td>{{ $student['yesterdayQuestions'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection