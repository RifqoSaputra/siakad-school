<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Kata Sandi | Mutiara Academy</title>

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
                    Buat kata sandi baru
                </h1>
                <p class="login-subheading">
                    Silakan buat kata sandi baru untuk akun {{ $email }} dan konfirmasi ulang.
                </p>

                @if ($errors->has('token'))
                    <p class="field-error-text" style="margin-bottom: 16px;">
                        {{ $errors->first('token') }}
                    </p>
                @endif

                <form method="POST" action="{{ route('password.reset') }}" class="login-form" id="resetForm">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <input type="hidden" name="token" value="{{ $token }}">

                    {{-- PASSWORD FIELD --}}
                    <div class="field-group">
                        <label for="password" class="field-label">Kata Sandi Baru</label>

                        <div class="field-input-wrapper field-input-password">
                            <input id="password" type="password" name="password"
                                class="field-input @error('password') field-input-error @enderror"
                                placeholder="Kata sandi baru..." autocomplete="new-password">

                            <button type="button" class="password-toggle" aria-label="Toggle visibility">
                                <span class="material-symbols-rounded">visibility_off</span>
                            </button>
                        </div>

                        @error('password')
                            <p class="field-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- PASSWORD CONFIRM --}}
                    <div class="field-group password-group">
                        <label for="password_confirmation" class="field-label">Konfirmasi Kata Sandi Baru</label>

                        <div class="field-input-wrapper field-input-password">
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                class="field-input @error('password_confirmation') field-input-error @enderror"
                                placeholder="Konfirmasi kata sandi..." autocomplete="new-password">

                            <button type="button" class="password-toggle" aria-label="Toggle visibility">
                                <span class="material-symbols-rounded">visibility_off</span>
                            </button>
                        </div>

                        @error('password_confirmation')
                            <p class="field-error-text">{{ $message }}</p>
                        @enderror
                        <p class="helper-text helper-text-error" id="matchHint" style="display: none;">Konfirmasi belum sesuai.</p>
                    </div>

                    {{-- BUTTON --}}
                    <button type="submit" class="login-button login-button-disabled" id="savePasswordButton" disabled>
                        Simpan Kata Sandi
                    </button>
                </form>

                <div class="helper-row">
                    <a class="text-link" href="{{ route('login') }}">Kembali ke login</a>
                </div>

            </div>
        </div>

    </div>

    <script>
        (function() {
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            const saveButton = document.getElementById('savePasswordButton');
            const matchHint = document.getElementById('matchHint');
            const toggles = document.querySelectorAll('.password-toggle');

            function updateButtonState() {
                const pwd = passwordInput.value.trim();
                const confirm = confirmInput.value.trim();
                const hasValue = pwd !== '' && confirm !== '';
                const matched = pwd === confirm;

                if (hasValue && matched) {
                    saveButton.disabled = false;
                    saveButton.classList.remove('login-button-disabled');
                    saveButton.classList.add('login-button-active');
                    matchHint.style.display = 'none';
                } else {
                    saveButton.disabled = true;
                    saveButton.classList.add('login-button-disabled');
                    saveButton.classList.remove('login-button-active');
                    matchHint.style.display = hasValue && !matched ? 'block' : 'none';
                }
            }

            passwordInput.addEventListener('input', updateButtonState);
            confirmInput.addEventListener('input', updateButtonState);
            updateButtonState();

            toggles.forEach((toggle, index) => {
                toggle.addEventListener('click', function() {
                    const input = index === 0 ? passwordInput : confirmInput;
                    const showing = input.type === 'text';
                    input.type = showing ? 'password' : 'text';
                    const icon = toggle.querySelector('.material-symbols-rounded');
                    icon.textContent = showing ? 'visibility_off' : 'visibility';
                });
            });
        })();
    </script>
</body>

</html>
