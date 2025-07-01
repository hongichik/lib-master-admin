{{-- filepath: /Users/admin/Documents/code/thu_vien/laravel/vendor/hongdev/master-admin/resources/views/master-admin/layout/partials/sidebar.blade.php --}}
<aside class="app-sidebar {{ config('masteradmin.sidebar.theme.class', 'bg-body-secondary shadow') }}" data-bs-theme="{{ config('masteradmin.sidebar.theme.dark_mode', true) ? 'dark' : 'light' }}">
    {{-- Sidebar Brand --}}
    <div class="sidebar-brand">
        <a href="{{ url(config('masteradmin.sidebar.brand.url', '/admin')) }}" class="brand-link">
            <img src="{{ asset(config('masteradmin.sidebar.brand.logo', 'vendor/master-admin/assets/img/logoIT.png')) }}" 
                 alt="{{ config('masteradmin.sidebar.brand.text', 'Admin') }} Logo" 
                 class="brand-image">
            <span class="brand-text fw-light">{{ config('masteradmin.sidebar.brand.text', 'Master Admin') }}</span>
        </a>
    </div>

    {{-- Sidebar Wrapper --}}
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            {{-- Sidebar Menu --}}
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation" aria-label="Main navigation" data-accordion="false" id="navigation">
                @foreach(config('masteradmin.sidebar.menu', []) as $menuItem)
                    @if(isset($menuItem['permission']) && !auth()->user()->can($menuItem['permission']))
                        @continue
                    @endif
                    
                    <li class="nav-item {{ isset($menuItem['submenu']) ? 'has-treeview' : '' }} {{ isset($menuItem['active']) && request()->is($menuItem['active']) ? 'menu-open' : '' }}">
                        <a href="{{ isset($menuItem['url']) ? url($menuItem['url']) : '#' }}" 
                           class="nav-link {{ isset($menuItem['active']) && request()->is($menuItem['active']) ? 'active' : '' }}">
                            <i class="nav-icon {{ $menuItem['icon'] ?? 'bi bi-circle' }}"></i>
                            <p>
                                {{ $menuItem['text'] }}
                                @if(isset($menuItem['submenu']))
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                @endif
                                @if(isset($menuItem['badge']))
                                    <span class="nav-badge badge text-bg-{{ $menuItem['badge']['type'] ?? 'secondary' }} me-3">
                                        {{ $menuItem['badge']['text'] }}
                                    </span>
                                @endif
                            </p>
                        </a>
                        
                        @if(isset($menuItem['submenu']))
                            <ul class="nav nav-treeview">
                                @foreach($menuItem['submenu'] as $submenu)
                                    @if(isset($submenu['permission']) && !auth()->user()->can($submenu['permission']))
                                        @continue
                                    @endif
                                    
                                    <li class="nav-item">
                                        <a href="{{ isset($submenu['url']) ? url($submenu['url']) : '#' }}" 
                                           class="nav-link {{ isset($submenu['active']) && request()->is($submenu['active']) ? 'active' : '' }}">
                                            <i class="nav-icon {{ $submenu['icon'] ?? 'bi bi-circle' }}"></i>
                                            <p>{{ $submenu['text'] }}</p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</aside>