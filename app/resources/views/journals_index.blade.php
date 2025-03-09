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
            <form action="{{ route('journals.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">学習目標</label>
                    <input type="text" name="goals" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">学習内容</label>
                    <textarea name="learnings" class="form-control" rows="2" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">疑問点</label>
                    <textarea name="questions" class="form-control" rows="2"></textarea>
                </div>

                <!-- ✅ 学習開始時間 -->
                <input type="hidden" name="start_time" id="start_time">
                <input type="hidden" name="end_time" id="end_time">
                <input type="hidden" name="duration" id="duration">

                <!-- 🟢 学習開始ボタン -->
                <button type="button" class="btn btn-success w-100" id="startButton">学習開始</button>
                <!-- 🔴 学習終了ボタン -->
                <button type="submit" class="btn btn-danger w-100 mt-2" id="endButton" disabled>学習終了</button>
            </form>
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
//学習開始・終了
document.addEventListener("DOMContentLoaded", function() {
    let startTime = null;

    function formatDateForMySQL(date) {
        return date.getFullYear() + '-' +
            ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
            ('0' + date.getDate()).slice(-2) + ' ' +
            ('0' + date.getHours()).slice(-2) + ':' +
            ('0' + date.getMinutes()).slice(-2) + ':' +
            ('0' + date.getSeconds()).slice(-2);
    }

    document.getElementById("startButton").addEventListener("click", function() {
        startTime = new Date();
        document.getElementById("start_time").value = formatDateForMySQL(startTime);
        document.getElementById("startButton").disabled = true;
        document.getElementById("endButton").disabled = false;
    });

    document.getElementById("endButton").addEventListener("click", function() {
        if (!startTime) return;

        let endTime = new Date();
        document.getElementById("end_time").value = formatDateForMySQL(endTime);

        let duration = Math.round((endTime - startTime) / 1000); // 秒単位
        document.getElementById("duration").value = duration;
    });
});
</script>
@endsection