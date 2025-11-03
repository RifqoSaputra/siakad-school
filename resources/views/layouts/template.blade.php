<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | SIAKAD SCHOOL</title>
    {{-- Memuat asset via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    {{-- CSS Layout Dasar (Untuk mengatasi tampilan yang kacau) --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

        /* ----------------------------------- */
        /* CSS UTAMA UNTUK MENGATUR LAYOUT */
        /* ----------------------------------- */
        body {
            margin: 0;
            padding: 0;
            display: flex;
            /* KUNCI: Membuat Sidebar dan Main-Content berdampingan */
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background-color: #f4f7f6;
            /* Latar belakang umum */
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background-color: #ffffff;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
            /* Ubah padding vertikal */
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Gaya Logo di Sidebar */
        .sidebar img {
            width: 50px;
            height: 50px;
            margin-bottom: 10px;
        }

        .sidebar p {
            font-size: 0.85rem;
            text-align: center;
            line-height: 1.2;
            margin-bottom: 30px;
            font-weight: 600;
            color: #4A46E0;
        }

        /* Container Menu */
        .menu-container {
            width: 100%;
            padding: 0 15px;
            /* Sesuaikan padding horizontal */
        }

        /* Gaya Link Menu */
        .sidebar a {
            display: block;
            color: #343a40;
            text-decoration: none;
            padding: 10px 15px;
            margin-bottom: 5px;
            border-radius: 8px;
            transition: all 0.2s;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .sidebar a:hover {
            background-color: #e9ecef;
            color: #4A46E0;
        }

        .sidebar a.active {
            background-color: #4A46E0;
            color: white;
        }

        .sidebar a.dropdown-toggle.active-dropdown {
            background-color: #4A46E0;
            color: white;
        }

        /* Status Awal: Tersembunyi */
        .dropdown-menu-custom {
            display: none;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            /* Untuk animasi smooth */
        }

        /* Status Aktif: Tampilkan */
        .dropdown-menu-custom.show {
            display: block;
            max-height: 500px;
            /* Nilai besar agar konten terlihat */
        }

        /* Area Konten Utama */
        .main-content {
            margin-left: 250px;
            /* PENTING: Menggeser konten agar tidak tertutup sidebar */
            flex-grow: 1;
            padding: 0;
            width: calc(100% - 250px);
            min-height: 100vh;
        }

        .page-container {
            padding: 20px;
        }

        /* Topbar */
        .topbar {
            background-color: #fff;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        /* Breadcrumb (Bootstrap style, pastikan Bootstrap CSS dimuat via Vite) */
        .breadcrumb {
            --bs-breadcrumb-divider: '>';
            /* Menggunakan divider Bootstrap */
        }

        /* Profile Section */
        .profile-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* Profile Dropdown */
        .profile-dropdown {
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .profile-dropdown img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .profile-dropdown span {
            font-weight: 600;
        }

        /* Profile Dropdown Menu */
        .profile-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 15px;
            width: 250px;
            display: none;
            z-index: 10;
            margin-top: 10px;
        }

        .profile-dropdown-menu.active {
            display: block;
        }

        .btn-profile {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: none;
            border-radius: 6px;
            background-color: #4A46E0;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-profile:hover {
            filter: brightness(0.9);
        }

        /* Logout Link di Sidebar */
        .logout-link {
            /* ... (CSS Anda sebelumnya) ... */
            margin-top: auto;
            /* Mendorong ke bawah */
        }

        /* Pastikan elemen Dashboard tidak memiliki link ganda */
        .dashboard-link-container {
            width: 100%;
            padding: 0 15px;
        }
    </style>
</head>

<body>
    @php
        // Ambil data user yang sedang login
        $user = Auth::user();
    @endphp

    <div class="sidebar">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Sekolah">
        <p>Sistem Informasi Akademik<br>Sekolah Mutiara Bangsa</p>

        <div class="menu-container">
            {{-- Menu Dashboard (Tunggal) --}}
            <a href="{{ route('dashboard.index') }}"
                class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                <i class="bi bi-house-door me-2"></i> Dashboard
            </a>

            {{-- Looping Menu Dinamis --}}
            @foreach ($user->getAllowedParentMenus() as $menu)
                @if ($menu->have_child)
                    {{-- Menu memiliki Sub-menu (Dropdown) --}}
                    <a href="#" class="dropdown-toggle" id="menu-{{ $menu->menu_id }}">
                        <i class="{{ $menu->icon }} me-2"></i> {{ $menu->nama_menu }}
                    </a>

                    <div class="dropdown-menu-custom" style="padding-left: 20px; margin-top: -5px; display: none;">
                        @foreach ($menu->children as $subMenu)
                            {{-- Looping Sub-Menu (Level 2) --}}
                            <a href="{{ url($subMenu->url) }}"
                                class="{{ request()->is(trim($subMenu->url, '/')) ? 'active' : '' }}"
                                style="padding: 5px 15px; font-size: 0.9rem;">
                                - {{ $subMenu->nama_menu }}
                            </a>
                        @endforeach
                    </div>
                @else
                    {{-- Menu Tunggal (Tidak Punya Anak) --}}
                    <a href="{{ url($menu->url) }}"
                        class="nav-link {{ request()->is(trim($menu->url, '/')) ? 'active' : '' }}">
                        <i class="{{ $menu->icon }} me-2"></i> {{ $menu->nama_menu }}
                    </a>
                @endif
            @endforeach
        </div>

        <div class="logout-link">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
            </form>
        </div>

    </div>

    <div class="main-content">
        <div class="topbar">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">@yield('breadcrumb')</li>
                </ol>
            </nav>

            <div class="profile-section">
                <div class="notification">
                    <i class="bi bi-bell"></i>
                    <span class="notification-badge">3</span>
                </div>

                <div class="profile-dropdown" id="profileToggle">
                    <img src="{{ asset('images/default-user.png') }}" alt="Profile">
                    {{-- KOREKSI: Menggunakan username bukan name --}}
                    <span>{{ $user->username ?? 'User' }}</span>
                    <i class="bi bi-caret-down-fill" style="font-size: 0.7rem;"></i>

                    <div class="profile-dropdown-menu" id="profileMenu">
                        <img src="{{ asset('images/default-user.png') }}" alt="Profile"
                            style="width:60px;height:60px;border-radius:50%;display:block;margin:auto;">
                        {{-- KOREKSI: Menggunakan username bukan name --}}
                        <p style="text-align: center; margin-top: 10px; font-weight: bold;">
                            {{ $user->username ?? 'User' }}</p>

                        {{-- Asumsi $user->role adalah relasi hasOne dari User ke Role --}}
                        <p style="text-align: center; font-size: 14px; color: #6c757d; margin-bottom: 5px;">
                            {{ Str::upper($user->roles->first()->deskripsi ?? 'N/A') }}
                        </p>

                        <button class="btn-profile" onclick="window.location.href='{{ route('profile') }}'">Setting
                            Profile</button>

                        <form action="{{ route('logout') }}" method="POST" style="margin-top: 5px;">
                            @csrf
                            <button class="btn-profile" type="submit"
                                style="background-color: #fce8e8; color: #dc3545;">
                                <i class="bi bi-box-arrow-right me-1"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-container">
            @yield('content')
        </div>
    </div>

    <script>
        // Script untuk toggle profile dropdown
        const profileToggle = document.getElementById('profileToggle');
        const profileMenu = document.getElementById('profileMenu');

        profileToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            profileMenu.classList.toggle('active');
        });
        document.addEventListener('click', (e) => {
            // Pastikan klik di luar dropdown (termasuk tombolnya) akan menutup
            if (!profileToggle.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.remove('active');
            }
        });
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const submenu = this.nextElementSibling;

                // Toggle visibility (Menggunakan kelas 'show' dari CSS)
                if (submenu && submenu.classList.contains('dropdown-menu-custom')) {
                    submenu.classList.toggle('show');
                }

                // Toggle status active pada link parent
                this.classList.toggle('active-dropdown');
            });
        });
    </script>
</body>

</html>
