<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | Mutiara Academy</title>

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    {{-- Material Symbols --}}
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="login-body">
    <div class="login-page">

        {{-- BAGIAN KIRI --}}
        <div class="login-left">

            <div class="login-left-image" style="background-image: url('{{ asset('images/bg-login.jpg') }}');">
            </div>

            <div class="login-left-overlay"></div>

            <div class="quote-wrapper">
                <p class="quote-text">
                    <b>“Hanya dengan Pendidikan kita akan tumbuh menjadi suatu bangsa.”</b>
                </p>

                <p class="quote-author">Dewi Sartika</p>
            </div>
        </div>

        {{-- BAGIAN KANAN --}}
        <div class="login-right">
            <div class="login-panel">

                {{-- LOGO --}}
                <div class="login-logo-wrapper">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="login-logo">
                </div>

                {{-- HEADING --}}
                <div class="demo-badge">
                    <span class="material-symbols-rounded" style="font-size: 16px;">science</span>
                    Mode Pameran
                </div>

                <h1 class="login-heading">
                    Pilih Role untuk<br>Masuk
                </h1>
                <p class="login-subheading">
                    Klik salah satu role untuk menjelajahi dashboard
                </p>

                {{-- Error message for demo login --}}
                @if ($errors->has('demo'))
                    <div class="toast toast-error" style="background: #FEF2F2; border-color: #FECACA; color: #991B1B;">
                        <span class="material-symbols-rounded" style="color: #DC2626;">error</span>
                        <div class="toast-text">{{ $errors->first('demo') }}</div>
                    </div>
                @endif

                @if (session('password_reset_success'))
                    <div class="toast toast-success" id="resetToast">
                        <span class="material-symbols-rounded">check_circle</span>
                        <div class="toast-text">{{ session('password_reset_success') }}</div>
                        <button type="button" class="toast-close" aria-label="Tutup notifikasi">&times;</button>
                    </div>
                @endif

                {{-- ROLE CARDS --}}
                <div class="role-cards">
                    {{-- ADMIN --}}
                    <form method="POST" action="{{ route('login.demo') }}">
                        @csrf
                        <input type="hidden" name="role" value="Admin">
                        <button type="submit" class="role-card role-card-admin" id="btnLoginAdmin">
                            <div class="role-card-icon role-card-icon-admin">
                                <span class="material-symbols-rounded">admin_panel_settings</span>
                            </div>
                            <div class="role-card-info">
                                <span class="role-card-title">Admin</span>
                                <span class="role-card-desc">Kelola data sekolah, siswa & guru</span>
                            </div>
                            <span class="material-symbols-rounded role-card-arrow">arrow_forward</span>
                        </button>
                    </form>

                    {{-- GURU --}}
                    <form method="POST" action="{{ route('login.demo') }}">
                        @csrf
                        <input type="hidden" name="role" value="Guru">
                        <button type="submit" class="role-card role-card-guru" id="btnLoginGuru">
                            <div class="role-card-icon role-card-icon-guru">
                                <span class="material-symbols-rounded">school</span>
                            </div>
                            <div class="role-card-info">
                                <span class="role-card-title">Guru</span>
                                <span class="role-card-desc">Absensi, nilai & jadwal mengajar</span>
                            </div>
                            <span class="material-symbols-rounded role-card-arrow">arrow_forward</span>
                        </button>
                    </form>

                    {{-- ORANG TUA --}}
                    <form method="POST" action="{{ route('login.demo') }}">
                        @csrf
                        <input type="hidden" name="role" value="Orang Tua">
                        <button type="submit" class="role-card role-card-ortu" id="btnLoginOrtu">
                            <div class="role-card-icon role-card-icon-ortu">
                                <span class="material-symbols-rounded">family_restroom</span>
                            </div>
                            <div class="role-card-info">
                                <span class="role-card-title">Orang Tua</span>
                                <span class="role-card-desc">Pantau nilai, absensi & rapor anak</span>
                            </div>
                            <span class="material-symbols-rounded role-card-arrow">arrow_forward</span>
                        </button>
                    </form>
                </div>

                {{-- ============================================
                     ORIGINAL LOGIN FORM (commented out for exhibition)
                     Uncomment this section and remove the role cards
                     above to restore normal email/password login.
                ============================================ --}}
                {{--
                <h1 class="login-heading">
                    Masuk ke<br>Mutiara Academy
                </h1>
                <p class="login-subheading">
                    Sistem Akademik SMK Mutiara Bangsa 1
                </p>

                @if (session('password_reset_success'))
                    <div class="toast toast-success" id="resetToast">
                        <span class="material-symbols-rounded">check_circle</span>
                        <div class="toast-text">{{ session('password_reset_success') }}</div>
                        <button type="button" class="toast-close" aria-label="Tutup notifikasi">&times;</button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.process') }}" class="login-form" id="loginForm">
                    @csrf

                    <div class="field-group">
                        <label for="email" class="field-label">Email</label>
                        <div class="field-input-wrapper">
                            <input id="email" type="email" name="email"
                                value="{{ session('login_email', old('email')) }}" placeholder="Email..."
                                class="field-input @error('email') field-input-error @enderror" autocomplete="email"
                                autofocus>
                        </div>
                        @error('email')
                            <p class="field-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group password-group">
                        <label for="password" class="field-label">Kata Sandi</label>
                        <div class="field-input-wrapper field-input-password">
                            <input id="password" type="password" name="password" value="{{ old('password') }}"
                                class="field-input @error('password') field-input-error @enderror"
                                placeholder="Kata sandi..." autocomplete="current-password">
                            <button type="button" class="password-toggle" aria-label="Toggle visibility">
                                <span class="material-symbols-rounded">visibility_off</span>
                            </button>
                        </div>
                        @error('password')
                            <p class="field-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="helper-row helper-row-compact">
                        <a class="text-link" href="{{ route('password.request') }}">Lupa kata sandi?</a>
                    </div>

                    <button type="submit" class="login-button login-button-disabled" id="loginButton" disabled>
                        Masuk
                    </button>
                </form>
                --}}

            </div>
        </div>

    </div>

    <script>
        (function() {
            // Toast close handler
            const toast = document.getElementById('resetToast');
            const toastClose = toast?.querySelector('.toast-close');
            if (toast && toastClose) {
                toastClose.addEventListener('click', () => toast.remove());
            }
        })();
    </script>

</body>

</html>
