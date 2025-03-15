@extends('layouts.app')

@section('content')
<div class="container">
    <h2>学習ジャーナル</h2>
    
    <div class="row">
        <!-- 📊 グラフエリア -->
        <div class="col-md-8">
            <canvas id="learningChart"></canvas>
        </div>

        <!-- 📝 フォームエリア -->
        <div class="col-md-4">
            <form id="journalForm">
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
                <input type="hidden" name="start_time" id="start_time">
                <input type="hidden" name="end_time" id="end_time">
                <input type="hidden" name="duration" id="duration">
                <button type="button" class="btn btn-success w-100" id="startButton">学習開始</button>
                <button type="submit" class="btn btn-danger w-100 mt-2" id="endButton" disabled>学習終了</button>
            </form>
        </div>
    </div>

    <!-- 🔍 検索フォーム -->
    <h3>記録一覧</h3>
    <form id="searchForm" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <label>開始日:</label>
                <input type="date" name="start_date" class="form-control">
            </div>
            <div class="col-md-3">
                <label>終了日:</label>
                <input type="date" name="end_date" class="form-control">
            </div>
            <div class="col-md-4">
                <label>学習内容・目標・疑問を検索:</label>
                <input type="text" name="keyword" class="form-control" placeholder="例: 三角関数">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">検索</button>
            </div>
        </div>
    </form>

    <!-- 📄 学習記録のテーブル -->
    <div class="mt-4">
        <div id="journalList">
            @include('journals_list')
        </div>

        <!-- 📌 ページネーション（非同期リクエスト対応） -->
        <div class="d-flex justify-content-center" id="pagination">
            {!! $journals->appends(request()->query())->links() !!}
        </div>
    </div>
</div>

<!-- 📊 Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    let csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let startTime = null;

    function formatDateForMySQL(date) {
    return date.getFullYear() + '-' +
        String(date.getMonth() + 1).padStart(2, '0') + '-' +
        String(date.getDate()).padStart(2, '0') + ' ' +
        String(date.getHours()).padStart(2, '0') + ':' +
        String(date.getMinutes()).padStart(2, '0') + ':' +
        String(date.getSeconds()).padStart(2, '0');
}

    function initJournalHandlers() {
        document.getElementById("startButton").addEventListener("click", function() {
            startTime = new Date();
            document.getElementById("start_time").value = formatDateForMySQL(startTime);
            document.getElementById("startButton").disabled = true;
            document.getElementById("endButton").disabled = false;
        });

        document.getElementById("endButton").addEventListener("click", function() {
            if (!startTime) {
                alert("学習開始ボタンを押してください！");
                return;
            }
            
            let endTime = new Date();
            let durationInSeconds = Math.round((endTime - startTime) / 1000);

            if (durationInSeconds <= 0) {
                alert("学習時間が正しく記録されていません。");
                return;
            }

            document.getElementById("end_time").value = formatDateForMySQL(endTime);
            document.getElementById("duration").value = durationInSeconds;
        });

        document.getElementById("journalForm").addEventListener("submit", function(event) {
            event.preventDefault();
            let formData = new FormData(this);
            
            fetch("{{ route('journals.store') }}", {
                method: "POST",
                body: formData,
                headers: { 
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json"
                }
            })
            .then(response => {
                if (!response.ok) throw new Error("サーバーエラー");
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert("学習記録が追加されました！");
                    location.reload();
                } else {
                    alert("エラー: " + JSON.stringify(data.errors));
                }
            })
            .catch(error => console.error("エラー:", error));
        });
    }

    function fetchJournalData() {
    document.getElementById("searchForm").addEventListener("submit", function(event) {
        event.preventDefault();
        let url = new URL("{{ route('journals.index') }}", window.location.origin);
        let params = new URLSearchParams(new FormData(this));

        fetch(url + "?" + params.toString(), { 
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(response => response.json())
        .then(data => { 
            document.getElementById("journalList").innerHTML = data.html;
            document.getElementById("pagination").innerHTML = data.pagination; // 📌 ページネーションを更新
        })
        .catch(error => console.error("検索エラー:", error));
    });

    document.addEventListener("click", function(e) {
    if (e.target.tagName === "A" && e.target.closest("#pagination")) {
        e.preventDefault();
        fetch(e.target.href, { 
            headers: { "X-Requested-With": "XMLHttpRequest" } // ✅ AJAXリクエストを明示
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTPエラー: ${response.status}`);
            }
            return response.json();
        })
        .then(data => { 
            document.getElementById("journalList").innerHTML = data.html;
            document.getElementById("pagination").innerHTML = data.pagination;
        })
        .catch(error => console.error("ページネーションエラー:", error));
    }
});
    }

    function renderChart() {
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
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    }

    initJournalHandlers();
    fetchJournalData();
    renderChart();
});
</script>
@endsection