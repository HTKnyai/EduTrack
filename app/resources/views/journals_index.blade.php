@extends('layouts.app')

@section('content')
<div class="container">
    <h2>学習ジャーナル</h2>

    <div class="row">
        <!-- 📊 グラフエリア（左側） -->
        <div class="col-md-8">
            <canvas id="learningChart"></canvas>
        </div>

        <!-- 📝 フォームエリア（右側） -->
        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">学習目標</label>
                <input type="text" name="goals" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">学習内容</label>
                <textarea name="learnings" class="form-control" rows="2"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">疑問点</label>
                <textarea name="questions" class="form-control" rows="2"></textarea>
            </div>
            <div class="mb-3">
                <button class="btn btn-success w-100">学習開始</button>
            </div>
            <div class="mb-3">
                <button class="btn btn-danger w-100">学習終了</button>
            </div>
        </div>
    </div>

    <h3>記録一覧</h3>
    <!-- 🔍 検索フォーム -->
    <form action="{{ route('journals_index') }}" method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <label>開始日:</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label>終了日:</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-4">
                <label>学習内容・目標・疑問を検索:</label>
                <input type="text" name="keyword" class="form-control" placeholder="例: 三角関数" value="{{ request('keyword') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">検索</button>
            </div>
        </div>
    </form>

    <!-- 📄 学習記録のテーブル -->
    <div class="mt-4">
        <table class="table">
            <thead>
                <tr>
                    <th>ユーザー名</th>
                    <th>開始時間</th>
                    <th>終了時間</th>
                    <th>学習時間</th>
                    <th>学習目標</th>
                    <th>学習内容</th>
                    <th>疑問</th>
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

        <!-- 📌 ページネーション -->
        <div class="d-flex justify-content-center">
            {{ $journals->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- 📊 Chart.js のスクリプト -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var ctx = document.getElementById('learningChart').getContext('2d');

    var chartData = {
        labels: @json($weeklyData->pluck('date')),
        datasets: [{
            label: '学習時間（分）',
            data: @json($weeklyData->pluck('total_duration')->map(fn($d) => round($d / 60, 1))),
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
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
@endsection