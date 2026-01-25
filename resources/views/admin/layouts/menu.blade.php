@foreach($menus as $menu)
    <li class="nav-item">
        <a href="{{ $menu->route ? route($menu->route) : '#' }}" 
           class="nav-link {{ $menu->isActive() ? 'active' : '' }} d-flex justify-content-between align-items-center"
           @if($menu->children->count() > 0) 
                data-bs-toggle="collapse" 
                data-bs-target="#menu-{{ $menu->id }}" 
                aria-expanded="{{ $menu->isActive() ? 'true' : 'false' }}" 
           @endif>
            <div class="d-flex align-items-center">
                @if(isset($is_child) && $is_child)
                    <i class="bi bi-circle{{ $menu->isActive() ? '-fill' : '' }} me-2" style="font-size: 0.5rem; opacity: 0.7;"></i>
                @elseif($menu->icon)
                    <i class="{{ $menu->icon }} me-2"></i>
                @endif
                {{ $menu->title }}
            </div>
            @if($menu->children->count() > 0)
                <i class="bi bi-chevron-right dropdown-icon transition-transform" style="font-size: 0.8rem;"></i>
            @endif
        </a>
        @if($menu->children->count() > 0)
            <ul class="collapse list-unstyled ps-3 {{ $menu->isActive() ? 'show' : '' }}" id="menu-{{ $menu->id }}">
                @include('admin.layouts.menu', ['menus' => $menu->children, 'is_child' => true])
            </ul>
        @endif
    </li>
@endforeach
