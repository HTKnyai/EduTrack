@extends('layouts.app')

@section('title', '生徒管理')

@section('content')
<div class="container">
    <h2>生徒管理</h2>

    <!-- 検索フォーム -->
    <form action="{{ route('students.index') }}" method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="生徒名で検索" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="goal" class="form-control" placeholder="学習目標で検索" value="{{ request('goal') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="learning" class="form-control" placeholder="学習内容で検索" value="{{ request('learning') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="question" class="form-control" placeholder="疑問点で検索" value="{{ request('question') }}">
            </div>
            <div class="col-md-2 mt-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">検索</button>
            </div>
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