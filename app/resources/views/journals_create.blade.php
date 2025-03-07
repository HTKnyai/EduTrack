@extends('layouts.app')

@section('content')
<div class="container">
    <h2>学習ジャーナル</h2>

    <!-- グラフエリア -->
    <div class="mb-4">
        <canvas id="learningChart"></canvas>
    </div>


    <!-- 🔍 検索フォーム -->
    <form action="{{ route('journals_index') }}" method="GET" class="mb-3">
        <div class="row">
            <!-- 開始日 -->
            <div class="col-md-3">
                <label>開始日:</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            
            <!-- 終了日 -->
            <div class="col-md-3">
                <label>終了日:</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>

            <!-- キーワード検索 -->
            <div class="col-md-4">
                <label>学習内容を検索:</label>
                <input type="text" name="keyword" class="form-control" placeholder="例: 三角関数" value="{{ request('keyword') }}">
            </div>

            <!-- 検索ボタン -->
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">検索</button>
            </div>
        </div>
    </form>

    <!-- 📊 学習ジャーナル一覧 -->
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
                <td>{{ $journal->duration }} 分</td>
                <td>{{ $journal->goals }}</td>
                <td>{{ $journal->learnings }}</td>
                <td>{{ $journal->questions }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- 📌 ページネーション -->
    <div class="d-flex justify-content-center">
        {{ $journals->appends(request()->query())->links() }}
    </div>
</div>

<!-- Chart.js のスクリプト -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var ctx = document.getElementById('learningChart').getContext('2d');

    var chartData = {
        labels: @json($weeklyData->pluck('date')),
        datasets: [{
            label: '学習時間（秒）',
            data: @json($weeklyData->pluck('total_duration')),
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    };

    new Chart(ctx, {
        type: 'bar',
        data: chartData,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endsection