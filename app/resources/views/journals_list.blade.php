<table class="table">
    <thead>
        <tr>
            @if(auth()->user()->role == 1) <!-- 教師の場合のみ表示 -->
                <th>ユーザー名</th>
            @endif
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
            @if(auth()->user()->role == 1) <!-- 教師の場合のみ表示 -->
                <td>{{ $journal->user->name }}</td>
            @endif      
            <!--日時をCarbon変換 ただしモデルで行なっているのでparse()は本来不要-->
            <td>{{ \Carbon\Carbon::parse($journal->start_time)->format('Y-m-d H:i:s') }}</td>
            <td>{{ \Carbon\Carbon::parse($journal->end_time)->format('Y-m-d H:i:s') }}</td>
            <td>{{ $journal->duration }} 秒</td>
            <td>{{ $journal->goals }}</td>
            <td>{{ $journal->learnings }}</td>
            <td>{{ $journal->questions }}</td>
        </tr>
        @endforeach
    </tbody>
</table>