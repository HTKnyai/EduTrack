@extends('layouts.app')
@section('title', 'ダッシュボード')
@section('content')

<div class="container">
    @if(auth()->user()->role == 0) <!-- 生徒の場合 -->
        <div class="row">
            <!-- 左側エリア（学習ジャーナル） -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">学習ジャーナル</div>
                    <div class="card-body">
                        <!-- 昨日の学習記録 -->
                        @if(!empty($yesterdayJournal) && $yesterdayJournal->total_duration > 0)
                            <div class="mb-3">
                                <h5>昨日の学習記録</h5>
                                <p><strong>学習時間:</strong> {{ round($yesterdayJournal->total_duration / 60, 1) }} 分</p>
                                <p><strong>学習内容:</strong> {{ $yesterdayJournal->learnings ?? 'なし' }}</p>
                                <p><strong>疑問点:</strong> {{ $yesterdayJournal->questions ?? 'なし' }}</p>
                            </div>
                        @else
                            <p>昨日の学習記録はありません</p>
                        @endif

                        <!-- 学習時間グラフ -->
                        <div class="mt-4">
                            <h5>直近1週間の学習時間</h5>
                            <canvas id="learningChart"></canvas>
                        </div>

                        <a href="{{ route('journals.index') }}" class="btn btn-primary mt-3">もっと見る</a>
                    </div>
                </div>
            </div>

            <!-- 右側エリア（Q&Aと教材） -->
            <div class="col-md-4">
                @include('components.qa_list', ['qas' => $qas])
                @include('components.material_list', ['materials' => $materials])
            </div>
        </div>
    @else <!-- 教師の場合 -->
        <div class="row">
            <div class="col-md-12">
                <!-- 生徒管理 -->
                <div class="card mb-3">
                    <div class="card-header">生徒管理</div>
                    <div class="card-body">
                        <p>生徒の学習記録を管理</p>
                        <a href="{{ route('students.index') }}" class="btn btn-info">生徒管理画面へ</a>
                    </div>
                </div>

                @include('components.qa_list', ['qas' => $qas])
                @include('components.material_list', ['materials' => $materials])
            </div>
        </div>
    @endif
</div>

<!-- グラフ描画スクリプト -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('learningChart')?.getContext('2d');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($weeklyData->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('m/d'))) !!},
            datasets: [{
                label: '学習時間 (分)',
                data: {!! json_encode($weeklyData->pluck('total_duration')->map(fn($d) => round($d / 60, 1))) !!},
                backgroundColor: 'rgba(50, 160, 230, 0.5)',
                borderColor: 'rgba(50, 160, 230, 1)',
                borderWidth: 2
            }]
        },
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