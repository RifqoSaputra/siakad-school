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
                <h1 class="login-heading">
                    Masuk ke<br>Mutiara Academy
                </h1>
                <p class="login-subheading">
                    Sistem Akademik SMK Mutiara Bangsa 1
                </p>

                {{-- GLOBAL ERROR --}}
                @if ($errors->has('username'))
                    <p class="field-error-text" style="margin-bottom: 16px;">
                        {{ $errors->first('username') }}
                    </p>
                @endif

                <form method="POST" action="{{ route('login.process') }}" class="login-form" id="loginForm">
                    @csrf

                    {{-- USERNAME FIELD --}}
                    <div class="field-group">
                        <label for="username" class="field-label">Username</label>

                        <div class="field-input-wrapper">
                            <input id="username" type="text" name="username" value="{{ old('username') }}"
                                placeholder="Username..."
                                class="field-input @error('username') field-input-error @enderror"
                                autocomplete="username" autofocus>
                        </div>

                        @error('username')
                            <p class="field-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- PASSWORD FIELD (no label sesuai permintaan) --}}
                    <div class="field-group password-group">

                        <div class="field-input-wrapper field-input-password">
                            <input id="password" type="password" name="password" value="{{ old('password') }}"
                                class="field-input @error('password') field-input-error @enderror"
                                placeholder="Kata sandi..." autocomplete="current-password">

                            <button type="button" class="password-toggle" aria-label="Toggle visibility">
                                <span class="material-symbols-rounded">visibility_off</span>
                            </button>
                        </div>

                        {{-- error khusus password --}}
                        @error('password')
                            <p class="field-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- LOGIN BUTTON --}}
                    <button type="submit" class="login-button login-button-disabled" id="loginButton" disabled>
                        Masuk
                    </button>
                </form>

            </div>
        </div>

    </div>

    <script>
        (function() {
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const loginButton = document.getElementById('loginButton');
            const toggleButton = document.querySelector('.password-toggle');
            const toggleIcon = toggleButton?.querySelector('.material-symbols-rounded');

            function updateButtonState() {
                const canSubmit =
                    usernameInput.value.trim() !== '' &&
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

            usernameInput.addEventListener("input", updateButtonState);
            passwordInput.addEventListener("input", updateButtonState);

            updateButtonState();

            if (toggleButton) {
                toggleButton.addEventListener('click', function() {
                    const showing = passwordInput.type === 'text';
                    passwordInput.type = showing ? 'password' : 'text';
                    toggleIcon.textContent = showing ? 'visibility_off' : 'visibility';
                });
            }
        })();
    </script>

</body>

</html>
