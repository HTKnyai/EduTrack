<div class="card">
    <div class="card-header">教材一覧</div>
    <div class="card-body">
        <ul class="list-group">
            @forelse($materials as $material)
                <li class="list-group-item">
                    {{ $material->title }} - {{ $material->teacher->name }}
                </li>
            @empty
                <p class="text-muted">教材がありません。</p>
            @endforelse
        </ul>
        <a href="{{ route('materials.index') }}" class="btn btn-secondary mt-2">もっと見る</a>
    </div>
</div>