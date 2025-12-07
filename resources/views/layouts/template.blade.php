<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | SIAKAD SCHOOL</title>
    {{-- Memuat asset via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

        /* ----------------------------------- */
        /* CSS UTAMA UNTUK MENGATUR LAYOUT */
        /* ----------------------------------- */
        body {
            margin: 0;
            padding: 0;
            display: flex;
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background-color: #f4f7f6;
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
            flex-grow: 1;
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

        /* Dropdown Menu Sidebar */
        .dropdown-menu-custom {
            display: none;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .dropdown-menu-custom.show {
            display: block;
            max-height: 500px;
        }

        /* Area Konten Utama */
        .main-content {
            margin-left: 250px;
            flex-grow: 1;
            padding: 0;
            width: calc(100% - 250px);
            min-height: 100vh;
            background-color: #f4f7f6;
            /* Pastikan background-color tetap ada di sini */
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

        /* Breadcrumb */
        .breadcrumb {
            --bs-breadcrumb-divider: '>';
        }

        /* Profile Section */
        .profile-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* Notification */
        .notification {
            position: relative;
            cursor: pointer;
            font-size: 1.2rem;
            color: #6c757d;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 1px 6px;
            font-size: 0.7rem;
            font-weight: bold;
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
            margin-top: 20px;
            width: 100%;
            padding: 0 15px;
        }

        /* Gaya tombol logout di sidebar */
        .logout-link button {
            display: block;
            width: 100%;
            color: #dc3545;
            text-decoration: none;
            padding: 10px 15px;
            margin-bottom: 5px;
            border-radius: 8px;
            transition: all 0.2s;
            font-weight: 500;
            font-size: 0.95rem;
            background: none;
            border: 1px solid #dc3545;
            cursor: pointer;
        }

        .logout-link button:hover {
            background-color: #fce8e8;
        }

        /* --- Gaya Baru untuk Info Anak Orang Tua di Topbar --- */
        .siswa-ortu-container {
            position: relative;
            cursor: pointer;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 15px;
            /* Jarak antara dropdown anak dan breadcrumb */
        }

        .siswa-ortu-info {
            display: flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 20px;
            background-color: #fff;
            transition: background-color 0.2s;
            font-size: 0.9rem;
            border: 1px solid #e0e0e0;
            /* margin-right: 10px; Dihapus, diatur oleh topbar-left gap */
        }

        .siswa-ortu-info:hover {
            background-color: #f8f9fa;
        }

        .siswa-ortu-info .avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #4A46E0;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1rem;
            margin-right: 8px;
        }

        .siswa-ortu-info .nama-anak {
            font-weight: 600;
            color: #343a40;
            margin-right: 5px;
        }

        .siswa-ortu-info .kelas-anak {
            color: #6c757d;
            font-size: 0.85rem;
        }

        .siswa-ortu-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            width: 200px;
            padding: 10px 0;
            display: none;
            z-index: 9;
            margin-top: 5px;
        }

        .siswa-ortu-dropdown.active {
            display: block;
        }

        .siswa-ortu-dropdown a {
            display: flex;
            align-items: center;
            padding: 8px 15px;
            text-decoration: none;
            color: #343a40;
            transition: background-color 0.2s;
            font-size: 0.9rem;
        }

        .siswa-ortu-dropdown a:hover,
        .siswa-ortu-dropdown a.selected {
            background-color: #e9ecef;
        }

        .siswa-ortu-dropdown a.selected {
            font-weight: bold;
            color: #4A46E0;
        }

        /* CSS untuk menghilangkan pemisah (>) HANYA untuk Orang Tua */
        /* Bagian ini tidak lagi relevan karena seluruh breadcrumb akan disembunyikan */
        .breadcrumb-ortu .breadcrumb-item:first-child::before {
            content: none;
        }

        /* --- Akhir Gaya Baru Info Anak --- */
    </style>
</head>

<body>
    @php
        // Ambil data user yang sedang login
        $user = Auth::user();

        function getActiveMenuTrail($menus, $currentUrl)
        {
            foreach ($menus as $menu) {
                // Format URL untuk bandingkan
                $menuUrl = trim($menu->url, '/');

                // Jika menu ini cocok dengan URL sekarang
                if (($menuUrl !== '' && request()->is($menuUrl)) || request()->is($menuUrl . '/*')) {
                    return [$menu];
                }

                // Jika menu punya anak → cek rekursif
                if ($menu->have_child && $menu->children->count() > 0) {
                    $trail = getActiveMenuTrail($menu->children, $currentUrl);

                    if (!empty($trail)) {
                        array_unshift($trail, $menu); // masukkan parent ke depan
                        return $trail;
                    }
                }
            }

            return [];
        }

        $breadcrumbTrail = getActiveMenuTrail($user->getAllowedParentMenus(), request()->path());

        // 🚨 PERBAIKAN DIMULAI DI SINI: Tambahkan 'use' statement untuk Model
        use App\Models\SIAKAD\SCHOOL\Ortu;
        use App\Models\SIAKAD\SCHOOL\Siswa;
        // Asumsi model Kelas dan SiswaKelas juga ada di namespace ini
        use App\Models\SIAKAD\SCHOOL\Kelas;
        use App\Models\SIAKAD\SCHOOL\SiswaKelas;

        $isOrtu = false;
        $children = [];
        $selectedSiswa = null;

        // 2. Cek Role (PENTING untuk menghindari error jika Admin/Guru login)
        // Kita gunakan try-catch untuk menangani jika relasi/method 'hasRole' tidak ada di model User
        try {
            if ($user && $user->hasRole('Orang Tua')) {
                $isOrtu = true;
            }
        } catch (\Exception $e) {
            // Tangani error jika method hasRole() tidak ada, user mungkin belum setup
            $isOrtu = false;
        }

        if ($isOrtu) {
            // 3. Ambil data Ortu berdasarkan users_id
            $ortu = Ortu::where('users_id', $user->users_id)->first();

            if ($ortu) {
                // 4. Ambil SEMUA anak yang dimiliki Ortu ini
                // Menggunakan relasi 'siswa' yang ada di model Ortu (lebih bersih)
                $children = $ortu
                    ->siswa()
                    ->with([
                        'enrollment' => function ($query) {
                            // Ambil enrollment terbaru
                            $query->latest('tahun_ajaran');
                        },
                        'enrollment.kelas',
                    ]) // Eager load relasi kelas
                    ->get();

                // 5. Tentukan Siswa yang aktif/terpilih dari session
                $selectedSiswaId = session('selected_siswa_id');

                if ($selectedSiswaId && $children->contains('id_siswa', $selectedSiswaId)) {
                    // Jika ada session dan siswa tersebut valid
                    $selectedSiswa = $children->firstWhere('id_siswa', $selectedSiswaId);
                } else {
                    // Jika belum ada session, atau session tidak valid, gunakan anak pertama sebagai default
                    $selectedSiswa = $children->first();
                    // Simpan yang pertama ke session agar tidak kosong
                    if ($selectedSiswa) {
                        session()->put('selected_siswa_id', $selectedSiswa->id_siswa);
                    }
                }
            }
        }
    @endphp

    <div class="sidebar">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Sekolah">
        <p>Sistem Informasi Akademik<br>Sekolah Mutiara Bangsa</p>

        <div class="menu-container">

            {{-- Looping Menu Dinamis --}}
            {{-- ASUMSI: $user->getAllowedParentMenus() tersedia --}}
            @foreach ($user->getAllowedParentMenus() as $menu)
                @if ($menu->have_child)
                    {{-- Menu memiliki Sub-menu (Dropdown) --}}
                    <a href="#" class="dropdown-toggle" {{-- HILANGKAN active-dropdown DI SINI --}} id="menu-{{ $menu->menu_id }}">
                        <i class="fas fa-{{ $menu->icon }} me-2"></i> {{ $menu->nama_menu }}
                    </a>
                    <div class="dropdown-menu-custom" style="padding-left: 20px; margin-top: -5px;">
                        @foreach ($menu->children as $subMenu)
                            {{-- Looping Sub-Menu (Level 2) --}}
                            <a href="{{ url($subMenu->url) }}"
                                class="{{ request()->is(trim($subMenu->url, '/')) ? 'active' : '' }}"
                                style="padding: 5px 15px; font-size: 0.9rem;">
                                <i class="fas fa-{{ $subMenu->icon }} me-2" style="font-size: 0.8rem;"></i>
                                {{ $subMenu->nama_menu }}
                            </a>
                        @endforeach
                    </div>
                @else
                    {{-- Menu Tunggal --}}
                    @php
                        // Ambil URL menu utama tanpa slash di awal/akhir untuk perbandingan dasar
                        $baseUrl = trim($menu->url, '/');
                    @endphp

                    <a href="{{ url($menu->url) }}"
                        class="{{ request()->is($baseUrl) || request()->is($baseUrl . '/*') ? 'active' : '' }}">
                        <i class="fas fa-{{ $menu->icon }} me-2"></i> {{ $menu->nama_menu }}
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

            {{-- KONTEN BREADCRUMB DAN DROPDOWN ANAK DIGABUNG JADI SATU BARIS --}}
            <div class="topbar-left">

                {{-- DROPDOWN ANAK DITARUH DI SINI (BERLAKU UNTUK ORANG TUA) --}}
                @if ($isOrtu && $selectedSiswa)
                    <div class="siswa-ortu-container">
                        <div class="siswa-ortu-info" id="siswaToggle">
                            <span class="avatar">{{ substr($selectedSiswa->nama, 0, 1) }}</span>
                            <span class="nama-anak">{{ $selectedSiswa->nama }}</span>
                            {{-- PERBAIKAN: Mengambil kelas dari relasi enrollment --}}
                            <span
                                class="kelas-anak">{{ $selectedSiswa->enrollment->first()->kelas->nama_kelas ?? 'Belum ada kelas' }}</span>
                            <i class="bi bi-caret-down-fill" style="font-size: 0.7rem; margin-left: 5px;"></i>
                        </div>

                        {{-- Dropdown Menu Siswa --}}
                        <div class="siswa-ortu-dropdown" id="siswaMenu">
                            @foreach ($children as $siswa)
                                <a href="{{ route('siswa.select', $siswa->id_siswa) }}"
                                    class="@if ($siswa->id_siswa == $selectedSiswa->id_siswa) selected @endif">
                                    <span class="avatar"
                                        style="width:25px; height:25px; font-size: 0.8rem; margin-right: 10px;">{{ substr($siswa->nama, 0, 1) }}</span>
                                    <div>
                                        <div style="font-weight: 500;">{{ $siswa->nama }}</div>
                                        <div style="font-size: 0.75rem; color: #6c757d;">
                                            {{-- PERBAIKAN: Mengambil kelas dari relasi enrollment --}}
                                            {{ $siswa->enrollment->first()->kelas->nama_kelas ?? 'N/A' }}</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- BREADCRUMB HANYA DITAMPILKAN JIKA BUKAN ORANG TUA --}}
                @if (!$isOrtu)
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>

                            @foreach ($breadcrumbTrail as $index => $menu)
                                @if ($index === count($breadcrumbTrail) - 1)
                                    <li class="breadcrumb-item active" aria-current="page">{{ $menu->nama_menu }}</li>
                                @else
                                    <li class="breadcrumb-item">
                                        @php
                                            $href = is_string($menu->url) ? url($menu->url) : '#';
                                        @endphp

                                        <a href="{{ $href }}">{{ $menu->nama_menu }}</a>
                                    </li>
                                @endif
                            @endforeach
                        </ol>
                    </nav>
                @endif
            </div>

            <div class="profile-section">
                <div class="notification">
                    <i class="bi bi-bell"></i>
                    <span class="notification-badge">3</span>
                </div>

                <div class="profile-dropdown" id="profileToggle">
                    <img src="{{ asset('images/default-user.jpg') }}" alt="Profile">
                    <span>{{ $user->nama_lengkap ?? 'User' }}</span>
                    <i class="bi bi-caret-down-fill" style="font-size: 0.7rem;"></i>

                    <div class="profile-dropdown-menu" id="profileMenu">
                        <img src="{{ asset('images/default-user.jpg') }}" alt="Profile"
                            style="width:60px;height:60px;border-radius:50%;display:block;margin:auto;">
                        <p style="text-align: center; margin-top: 10px; font-weight: bold;">
                            {{ $user->nama_lengkap ?? 'User' }}</p>

                        <p style="text-align: center; font-size: 14px; color: #6c757d; margin-bottom: 5px;">
                            {{ Str::upper($user->roles->first()->deskripsi ?? 'N/A') }}
                        </p>

                        <button class="btn-profile" onclick="window.location.href='{{ route('dashboard') }}'">Setting
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
        const siswaToggle = document.getElementById('siswaToggle');
        const siswaMenu = document.getElementById('siswaMenu');

        profileToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            profileMenu.classList.toggle('active');
            // Pastikan dropdown siswa tertutup saat dropdown profile dibuka
            if (siswaMenu) siswaMenu.classList.remove('active');
        });

        if (siswaToggle) {
            siswaToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                siswaMenu.classList.toggle('active');
                // Pastikan dropdown profile tertutup saat dropdown siswa dibuka
                profileMenu.classList.remove('active');
            });
        }

        document.addEventListener('click', (e) => {
            // Tutup dropdown profile
            if (profileMenu && !profileToggle.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.remove('active');
            }
            // Tutup dropdown siswa
            if (siswaMenu && siswaToggle && !siswaToggle.contains(e.target) && !siswaMenu.contains(e.target)) {
                siswaMenu.classList.remove('active');
            }
        });

        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

        // Pastikan dropdown yang sedang aktif tetap terbuka saat dimuat
        document.addEventListener('DOMContentLoaded', () => {
            dropdownToggles.forEach(toggle => {
                const submenu = toggle.nextElementSibling;
                const isActiveChild = submenu && submenu.querySelector('a.active');

                if (isActiveChild) {
                    submenu.classList.add('show');
                }
            });
        });

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

        function showLoading() {
            document.getElementById('global-loading')?.classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('global-loading')?.classList.add('hidden');
        }
    </script>

    {{-- Menambahkan stack untuk skrip halaman spesifik --}}
    @stack('scripts')
    <x-toast />
</body>

</html>
