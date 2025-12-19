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

        <div class="login-left-image"
             style="background-image: url('{{ asset('images/bg-login.jpg') }}');">
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

            <form method="POST"
                  action="{{ route('login.process') }}"
                  class="login-form"
                  id="loginForm">
                @csrf

                    {{-- USERNAME FIELD --}}
                    <div class="field-group">
                        <label for="email" class="field-label">Email</label>

                        <div class="field-input-wrapper">
                            <input id="email" type="email" name="email" value="{{ session('login_email', old('email')) }}"
                                placeholder="Email..."
                                class="field-input @error('email') field-input-error @enderror"
                                autocomplete="email" autofocus>
                        </div>

                        @error('email')
                            <p class="field-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- PASSWORD FIELD --}}
                    <div class="field-group password-group">
                        <label for="password" class="field-label">Kata Sandi</label>

                    <div class="field-input-wrapper field-input-password">
                        <input id="password"
                               type="password"
                               name="password"
                               value="{{ old('password') }}"
                               class="field-input @error('password') field-input-error @enderror"
                               placeholder="Kata sandi..."
                               autocomplete="current-password">

                        <button type="button" class="password-toggle" aria-label="Toggle visibility">
                            <span class="material-symbols-rounded">visibility_off</span>
                        </button>
                    </div>

                        {{-- error khusus password --}}
                        @error('password')
                            <p class="field-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="helper-row helper-row-compact">
                        <a class="text-link" href="{{ route('password.request') }}">Lupa kata sandi?</a>
                    </div>

                {{-- LOGIN BUTTON --}}
                <button type="submit"
                        class="login-button login-button-disabled"
                        id="loginButton"
                        disabled>
                    Masuk
                </button>
            </form>

        </div>
    </div>

</div>

    <script>
        (function() {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const loginButton = document.getElementById('loginButton');
            const toggleButton = document.querySelector('.password-toggle');
            const toggleIcon = toggleButton?.querySelector('.material-symbols-rounded');
            const toast = document.getElementById('resetToast');
            const toastClose = toast?.querySelector('.toast-close');

            function updateButtonState() {
                const canSubmit =
                    emailInput.value.trim() !== '' &&
                    passwordInput.value.trim() !== '';

        if (canSubmit) {
            loginButton.disabled = false;
            loginButton.classList.remove('login-button-disabled');
            loginButton.classList.add('login-button-active');
        } else {
            loginButton.disabled = true;
            loginButton.classList.add('login-button-disabled');
            loginButton.classList.remove('login-button-active');
        }
    }

            emailInput.addEventListener("input", updateButtonState);
            passwordInput.addEventListener("input", updateButtonState);

    updateButtonState();

            if (toggleButton) {
                toggleButton.addEventListener('click', function() {
                    const showing = passwordInput.type === 'text';
                    passwordInput.type = showing ? 'password' : 'text';
                    toggleIcon.textContent = showing ? 'visibility_off' : 'visibility';
                });
            }

            if (toast) {
                const hideToast = () => toast.remove();

                toastClose?.addEventListener('click', hideToast);
            }
        })();
    </script>

</body>
</html>