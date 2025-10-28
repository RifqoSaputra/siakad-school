<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Siakad-School | Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    @vite(['resources/css/app.css'])
  </head>

  <body>
    <div class="container-fluid auth-wrap">
      <div class="row h-100 g-0">
        
        {{-- ini text dikiri der --}}
        <div class="col-lg-6 d-none d-lg-flex align-items-center">
          <div class="hero-content">
            <div class="brand-mini">
              <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo" />
              <span>Mutiara Bangsa</span>
            </div>

            <div class="hero-text">
              <h1>Wujudkan Impian</h1>
              <p>Raih impian dan cita-cita anak bersama dengan kami</p>
              <div class="carousel-dots mt-4">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
              </div>
            </div>
          </div>
        </div>

        {{-- ini login form der --}}
        <div class="col-lg-6 d-flex justify-content-center align-items-center form-side">
          <div class="glass-card">
            <h2>Hello, Welcome back</h2>

            @if (session('status'))
              <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <form method="POST" action="{{ route('login') }}" novalidate>
              @csrf

              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email"
                  class="form-control @error('email') is-invalid @enderror"
                  placeholder="Enter your Email" value="{{ old('email') }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="position-relative">
                  <input type="password" id="password" name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Enter your password" required>
                  <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-decoration-none px-3"
                    id="togglePw">
                    <i class="bi bi-eye" id="pwIcon"></i>
                  </button>
                </div>
              </div>

              <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="#" class="small muted-link">Forgot Password?</a>
                <div class="form-check ms-auto">
                  <input class="form-check-input" type="checkbox" id="remember" name="remember">
                  <label class="form-check-label" for="remember">Remember Me</label>
                </div>
              </div>

              <button class="btn btn-brand w-100 py-2">Sign In</button>
            </form>
          </div>
        </div>

      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      const pw = document.getElementById('password');
      const icon = document.getElementById('pwIcon');
      document.getElementById('togglePw').addEventListener('click', () => {
        const isText = pw.type === 'text';
        pw.type = isText ? 'password' : 'text';
        icon.classList.toggle('bi-eye', isText);
        icon.classList.toggle('bi-eye-slash', !isText);
      });
    </script>
  </body>
</html>