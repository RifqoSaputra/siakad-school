<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | SIAKAD SCHOOL</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('styles')
</head>

<body class="app-shell">
    @php
        $user = Auth::user();

        function getActiveMenuTrail($menus, $currentUrl)
        {
            foreach ($menus as $menu) {
                $menuUrl = trim($menu->url, '/');

                if (($menuUrl !== '' && request()->is($menuUrl)) || request()->is($menuUrl . '/*')) {
                    return [$menu];
                }

                if ($menu->have_child && $menu->children->count() > 0) {
                    $trail = getActiveMenuTrail($menu->children, $currentUrl);

                    if (!empty($trail)) {
                        array_unshift($trail, $menu);
                        return $trail;
                    }
                }
            }

            return [];
        }

        $breadcrumbTrail = getActiveMenuTrail($user->getAllowedParentMenus(), request()->path());

        $pageTitle = trim($__env->yieldContent('title'));

        if ($pageTitle === '') {
            $pageTitle = !empty($breadcrumbTrail) ? end($breadcrumbTrail)->nama_menu : 'Dashboard';
        }

        function menuMatches($url)
        {
            $path = trim(parse_url($url ?? '', PHP_URL_PATH) ?? '', '/');

            if ($path === '') {
                // Empty URL: treat as home/dashboard only.
                return request()->is('/') || request()->is('dashboard') || request()->is('dashboard/*');
            }

            return request()->is($path) || request()->is($path . '/*');
        }

        function mapMenuIcon($name, $iconField = null, $fallback = 'chevron_right')
        {
            $nameMap = [
                'beranda' => 'dashboard',
                'dashboard' => 'dashboard',
                'mata pelajaran' => 'import_contacts',
                'jadwal' => 'calendar_month',
                'daftar siswa' => 'group',
                'daftar guru' => 'school',
                'pengumuman' => 'campaign',
                'data master' => 'database',
                'manajemen user' => 'manage_accounts',
                'nilai siswa' => 'checklist',
                'nilai ujian' => 'edit',
                'laporan' => 'article',
            ];

            $iconMap = [
                'tachometer-alt' => 'dashboard',
                'tachometer' => 'dashboard',
                'database' => 'database',
                'chalkboard-teacher' => 'school',
                'user-graduate' => 'school',
                'door-open' => 'meeting_room',
                'door-closed' => 'meeting_room',
                'users-cog' => 'manage_accounts',
                'user-cog' => 'manage_accounts',
                'user' => 'person',
                'user-alt' => 'person',
                'user-check' => 'verified_user',
                'users' => 'group',
                'user-friends' => 'group',
                'calendar' => 'calendar_month',
                'calendar-alt' => 'calendar_month',
                'book' => 'menu_book',
                'book-open' => 'import_contacts',
                'import_contacts' => 'import_contacts',
                'clipboard-check' => 'task_alt',
                'clipboard-list' => 'checklist',
                'file-alt' => 'article',
                'pen-alt' => 'edit',
                'bullhorn' => 'campaign',
                'bell' => 'notifications',
            ];

            $key = strtolower(trim($name));
            if (isset($nameMap[$key])) {
                return $nameMap[$key];
            }

            $iconKey = strtolower(trim((string) $iconField));
            if (isset($iconMap[$iconKey])) {
                return $iconMap[$iconKey];
            }

            return $fallback;
        }

    @endphp

    <aside class="sidebar" aria-label="Sidebar">
        <div class="brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Sekolah">
            <div class="text">Mutiara<br>Academy</div>
        </div>

        <nav class="nav-group" aria-label="Menu">
            @foreach ($user->getAllowedParentMenus() as $menu)
                @php
                    $active = menuMatches($menu->url);
                    $iconName = mapMenuIcon($menu->nama_menu, $menu->icon);
                    $childActive = false;
                    if ($menu->have_child && $menu->children->count()) {
                        foreach ($menu->children as $childCheck) {
                            if (menuMatches($childCheck->url)) {
                                $childActive = true;
                                break;
                            }
                        }
                    }
                    $isOpen = $active || $childActive;
                    $childContainerId = 'nav-children-' . ($menu->id ?? $loop->index);
                @endphp
                <div class="nav-item {{ $menu->have_child ? 'has-children' : '' }} {{ $isOpen ? 'open' : '' }}">
                    @if ($menu->have_child && $menu->children->count())
                        <button type="button"
                            class="nav-link nav-toggle {{ $active ? 'active' : '' }}"
                            data-target="{{ $childContainerId }}"
                            aria-expanded="{{ $isOpen ? 'true' : 'false' }}">
                            <span class="nav-link-content">
                                <span class="material-symbols-rounded">{{ $iconName }}</span>
                                <span>{{ $menu->nama_menu }}</span>
                            </span>
                            <span class="chevron material-symbols-rounded" aria-hidden="true">expand_more</span>
                        </button>
                        <div class="nav-children" id="{{ $childContainerId }}" @if($isOpen) data-open="true" @endif>
                            @foreach ($menu->children as $child)
                                @php
                                    $childActive = menuMatches($child->url);
                                    $childIcon = mapMenuIcon($child->nama_menu, $child->icon);
                                @endphp
                                <a href="{{ url($child->url) }}" class="nav-link {{ $childActive ? 'active' : '' }}"
                                    style="padding-left: 8px;" @if($childActive) aria-current="page" @endif>
                                    <span class="nav-link-content">
                                        <span class="material-symbols-rounded">{{ $childIcon }}</span>
                                        <span>{{ $child->nama_menu }}</span>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <a href="{{ url($menu->url) }}" class="nav-link {{ $active ? 'active' : '' }}"
                            @if($active) aria-current="page" @endif>
                            <span class="nav-link-content">
                                <span class="material-symbols-rounded">{{ $iconName }}</span>
                                <span>{{ $menu->nama_menu }}</span>
                            </span>
                        </a>
                    @endif
                </div>
            @endforeach
        </nav>

        <div class="logout-block">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="logout-button" type="submit">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="main-area">
        <div class="topbar">
            <div class="topbar-left">
                <h1 class="topbar-title">{{ $pageTitle }}</h1>
            </div>
            <div class="topbar-right">
                <div class="user-chip" id="profileToggle">
                    <img src="{{ asset('images/default-user.jpg') }}" alt="User">
                    <div class="user-meta">
                        <span class="name">{{ $user->nama_lengkap ?? 'User' }}</span>
                        @php
                            $roleDisplay = $user->roles->first()->deskripsi
                                ?? $user->roles->first()->nama_role
                                ?? $user->roles->first()->name
                                ?? 'Role';
                        @endphp
                        <span class="role">{{ $roleDisplay }}</span>
                    </div>
                    <span class="material-symbols-rounded">expand_more</span>
                </div>
            </div>
        </div>

        <div class="page-container">
            @yield('content')
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggles = document.querySelectorAll('.nav-toggle[data-target]');

            toggles.forEach((toggle) => {
                toggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetId = toggle.getAttribute('data-target');
                    const container = toggle.closest('.nav-item');
                    const panel = targetId ? document.getElementById(targetId) : null;
                    const isOpen = container?.classList.toggle('open');

                    if (panel) {
                        panel.dataset.open = isOpen ? 'true' : '';
                    }
                    toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                });
            });
        });
    </script>

    @stack('scripts')
    <x-toast />
</body>

</html>
