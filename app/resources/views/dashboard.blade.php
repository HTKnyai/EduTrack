@extends('layouts.app')

@section('title', 'ダッシュボード')

@section('content')
<div class="container">
    <div class="row">
        <!-- 左側エリア（学習ジャーナル） -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">学習ジャーナル</div>
                <div class="card-body">
                    <!-- 昨日の学習記録 -->
                    @if(isset($yesterdayJournal))
                        <div class="mb-3">
                            <h5>昨日の学習記録</h5>
                            <p><strong>学習時間:</strong> {{ $yesterdayJournal->duration }} 分</p>
                            <p><strong>学習内容:</strong> {{ $yesterdayJournal->learnings }}</p>
                            <p><strong>疑問点:</strong> {{ $yesterdayJournal->questions }}</p>
                        </div>
                    @else
                        <p>昨日の学習記録はありません。</p>
                    @endif

                    <!-- 学習時間グラフ -->
                    <div class="mt-4">
                        <h5>直近1週間の学習時間</h5>
                        <canvas id="learningChart"></canvas>
                    </div>

                    <!-- もっと見るボタン -->
                    <a href="/journals" class="btn btn-primary mt-3">もっと見る</a>
                </div>
            </div>
        </div>

        <!-- 右側エリア（Q&Aと教材） -->
        <div class="col-md-4">
            <!-- Q&A -->
            <div class="card mb-3">
                <div class="card-header">Q&A（質問掲示板）</div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($qas as $qa)
                            <li class="list-group-item">
                                <strong>
                                    @if($qa->anonymize) 匿名 @else {{ $qa->user->name }} @endif
                                </strong>: {{ $qa->contents }}
                            </li>
                        @endforeach
                    </ul>
                    <a href="/qas" class="btn btn-secondary mt-2">もっと見る</a>
                </div>
            </div>

            <!-- 教材 -->
            <div class="card">
                <div class="card-header">教材一覧</div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($materials as $material)
                            <li class="list-group-item">
                                {{ $material->title }} - {{ $material->teacher->name }}
                            </li>
                        @endforeach
                    </ul>
                    <a href="/materials" class="btn btn-secondary mt-2">もっと見る</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- グラフ描画スクリプト -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById('learningChart').getContext('2d');
        var chartData = {
            labels: {!! json_encode($journals->pluck('start_time')->map(fn($date) => \Carbon\Carbon::parse($date)->format('m/d'))) !!},
            datasets: [{
                label: '学習時間 (分)',
                data: {!! json_encode($journals->pluck('duration')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2
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