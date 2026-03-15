<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Centarica</title>
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="login-page">
<div class="login-card">
    <div class="login-brand">
        <div class="icon"><i class="fas fa-graduation-cap"></i></div>
        <h4>Centarica</h4>
        <p>Student Management System</p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h5 class="fw-700 mb-1" style="color:#1a1d2e;">Welcome back</h5>
            <p class="text-muted mb-4" style="font-size:.85rem;">Sign in to your account to continue</p>

            @if($errors->any())
            <div class="alert alert-danger alert-sm py-2 px-3 mb-3" style="font-size:.85rem;border-radius:8px;">
                <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="example@email.com" autofocus required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="form-control d-flex justify-content-end align-items-center p-0">
                        <input type="password" name="password" id="inputPassword" style="width: 100%; padding: .375rem 1.75rem .375rem .75rem;" class="@error('password') is-invalid @enderror"
                               placeholder="••••••••" required>
                        <i class="position-absolute translate-middle-x fa-solid fa-eye-slash" id="togglePassword" style="cursor: pointer;"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" style="cursor: pointer;">
                        <label class="form-check-label" for="remember" style="font-size:.85rem;">Remember me</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </button>
            </form>
        </div>
    </div>
</div>
<script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#inputPassword');

    togglePassword.addEventListener('click', function (e) {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });
</script>
</body>
</html>
