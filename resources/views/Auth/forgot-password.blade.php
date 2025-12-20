<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi | Mutiara Academy</title>

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
                    Lupa kata sandi?
                </h1>
                <p class="login-subheading">
                    Masukkan username Anda dan kami akan mengirim tautan reset ke email terdaftar.
                </p>

                {{-- INFO SUCCESS --}}
                @if (session('reset_email_masked'))
                    <div class="info-card info-success">
                        <div class="info-card-icon">
                            <span class="material-symbols-rounded">mail</span>
                        </div>
                        <div>
                            <p class="info-card-title">Tautan sudah dikirim</p>
                            <p class="info-card-text">
                                Cek email <b>{{ session('reset_email_masked', 'r************@admin.mutiarabangsa.ac.id') }}</b> dan buka tautan untuk atur ulang
                                kata sandi Anda.
                            </p>
                        </div>
                    </div>

                    @if (session('reset_link'))
                        <a class="text-button" href="{{ session('reset_link') }}">
                            Buka tautan sekarang (nb: button untuk dummy)
                        </a>
                    @endif
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="login-form" id="forgotForm">
                    @csrf

                    {{-- USERNAME FIELD --}}
                    <div class="field-group">
                        <label for="email" class="field-label">Email</label>

                        <div class="field-input-wrapper">
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                placeholder="Email..."
                                class="field-input @error('email') field-input-error @enderror"
                                autocomplete="email" autofocus>
                        </div>

                        @error('email')
                            <p class="field-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- BUTTON --}}
                    <button type="submit" class="login-button login-button-disabled" id="forgotButton" disabled>
                        Kirim tautan
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
            const emailInput = document.getElementById('email');
            const forgotButton = document.getElementById('forgotButton');

            function updateButtonState() {
                const canSubmit = emailInput.value.trim() !== '';

                if (canSubmit) {
                    forgotButton.disabled = false;
                    forgotButton.classList.remove('login-button-disabled');
                    forgotButton.classList.add('login-button-active');
                } else {
                    forgotButton.disabled = true;
                    forgotButton.classList.add('login-button-disabled');
                    forgotButton.classList.remove('login-button-active');
                }
            }

            emailInput.addEventListener('input', updateButtonState);
            updateButtonState();
        })();
    </script>
</body>

</html>
