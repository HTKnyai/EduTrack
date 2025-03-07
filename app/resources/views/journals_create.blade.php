@extends('layouts.app')

@section('title', '学習ジャーナル登録')

@section('content')
    <div class="container">
        <h1>学習ジャーナルを追加</h1>
        <form action="/journals/store" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">目標</label>
                <input type="text" name="goals" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">学習内容</label>
                <input type="text" name="learnings" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">疑問点</label>
                <input type="text" name="questions" class="form-control">
            </div>

            {{-- 学習時間計測用ボタン --}}
            <div class="mb-3">
                <label class="form-label">学習時間計測</label>
                <div>
                    <button type="button" class="btn btn-success" id="start-btn">開始</button>
                    <button type="button" class="btn btn-danger" id="end-btn" disabled>終了</button>
                </div>
                <p id="timer-display">学習時間: 0秒</p>
                <input type="hidden" name="start_time" id="start_time">
                <input type="hidden" name="end_time" id="end_time">
                <input type="hidden" name="duration" id="duration">
            </div>

            <button type="submit" class="btn btn-primary">登録</button>
        </form>

        {{-- 直近1週間の学習時間グラフ --}}
        <h2 class="mt-5">直近1週間の学習時間</h2>
        <canvas id="learningChart"></canvas>
    </div>

    {{-- JavaScript --}}
    <script>
        let startTime = null;
        let timerInterval;

        document.getElementById('start-btn').addEventListener('click', function() {
            startTime = new Date();
            document.getElementById('start_time').value = startTime.toISOString();
            document.getElementById('start-btn').disabled = true;
            document.getElementById('end-btn').disabled = false;

            timerInterval = setInterval(function() {
                let elapsedTime = Math.floor((new Date() - startTime) / 1000);
                document.getElementById('timer-display').textContent = `学習時間: ${elapsedTime}秒`;
            }, 1000);
        });

        document.getElementById('end-btn').addEventListener('click', function() {
            clearInterval(timerInterval);
            let endTime = new Date();
            document.getElementById('end_time').value = endTime.toISOString();
            document.getElementById('end-btn').disabled = true;

            let duration = Math.floor((endTime - startTime) / 1000);
            document.getElementById('duration').value = duration;
        });

        // 直近1週間の学習時間グラフ
        document.addEventListener("DOMContentLoaded", function() {
            fetch('/journals/weekly-data')
                .then(response => response.json())
                .then(data => {
                    let ctx = document.getElementById('learningChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: '学習時間 (分)',
                                data: data.durations,
                                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: { beginAtZero: true }
                            }
                        }
                    });
                });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection