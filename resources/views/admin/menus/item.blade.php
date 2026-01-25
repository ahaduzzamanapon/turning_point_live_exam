<li class="dd-item" data-id="{{ $menu->id }}">
    <div class="dd-handle d-flex justify-content-between align-items-center">
        <div>
            @if($menu->icon)
                <i class="{{ $menu->icon }} me-2"></i>
            @endif
            <span class="fw-bold">{{ $menu->title }}</span>
            <small class="text-muted ms-2">{{ $menu->route }}</small>
        </div>
        <div class="dd-nodrag btn-group btn-group-sm">
            <a href="{{ route('admin.menus.edit', $menu->id) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                <i class="bi bi-pencil"></i>
            </a>
            <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @if($menu->children->count() > 0)
        <ol class="dd-list">
            @foreach($menu->children as $child)
                @include('admin.menus.item', ['menu' => $child])
            @endforeach
        </ol>
    @endif
</li>
