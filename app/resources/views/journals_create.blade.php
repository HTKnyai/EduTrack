@extends('layouts.app')

@section('content')
<div class="container">
    <h2>学習ジャーナル</h2>

    <!-- グラフエリア -->
    <div class="mb-4">
        <canvas id="learningChart"></canvas>
    </div>

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