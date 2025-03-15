<div class="card mb-3">
    <div class="card-header">Q&A（質問掲示板）</div>
    <div class="card-body">
        <ul class="list-group">
            @forelse($qas as $qa)
                <li class="list-group-item">
                    <strong>{{ $qa->anonymize ? '匿名' : $qa->user->name }}</strong>: {{ $qa->contents }}
                    <span class="text-muted">（{{ $qa->created_at->format('Y-m-d') }}）</span>
                </li>
            @empty
                <p class="text-muted">質問がありません。</p>
            @endforelse
        </ul>
        <a href="{{ route('qas.index') }}" class="btn btn-secondary mt-2">もっと見る</a>
    </div>
</div>