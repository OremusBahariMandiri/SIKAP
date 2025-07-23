{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIKAP') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-color: #1270fc;
            --primary-dark: #0a5bca;
            --primary-light: #e8f1ff;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8f9fc;
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        .login-container {
            height: 100vh;
            display: flex;
            align-items: stretch;
        }

        .login-image-container {
            flex: 1;
            display: none;
            position: relative;
            overflow: hidden;
        }

        .login-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .company-logo {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: white;
        }

        .image-overlay h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .image-overlay p {
            font-size: 1.1rem;
            max-width: 80%;
            line-height: 1.6;
        }

        .login-form-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2rem;
            background-color: white;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h2 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #6c757d;
        }

        .login-form {
            max-width: 400px;
            margin: 0 auto;
            width: 100%;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .form-control {
            padding: 0.75rem;
            border-radius: 0.375rem;
            border: 1px solid #ced4da;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(18, 112, 252, 0.25);
        }

        .form-check-label {
            color: #6c757d;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.875rem;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .login-footer {
            text-align: center;
            margin-top: 2rem;
            color: #6c757d;
            font-size: 0.875rem;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .mobile-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .mobile-logo i {
            font-size: 3rem;
            color: var(--primary-color);
        }

        .mobile-logo h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-top: 0.5rem;
        }

        .mobile-logo p {
            font-size: 0.875rem;
            color: #6c757d;
        }

        /* Password Toggle Styles */
        .password-input-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 0;
            font-size: 1rem;
            z-index: 10;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .password-toggle:focus {
            outline: none;
        }

        .password-input-container .form-control {
            padding-right: 2.5rem;
        }

        @media (min-width: 992px) {
            .login-image-container {
                display: block;
            }

            .mobile-logo {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Left Side - Image -->
        <div class="login-image-container">
            <img src="https://images.unsplash.com/photo-1497215842964-222b430dc094?q=80&w=1770&auto=format&fit=crop"
                alt="Office Dashboard" class="login-image">
            <div class="image-overlay">

                <h1>SIKAP</h1>
                <p>Sistem Informasi Kearsipan dan Administrasi Perusahaan</p>
                <p>&copy; {{ date('Y') }} Rev.01 </p>

            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-form-container">
            <!-- Mobile Logo (visible on small screens) -->
            <div class="mobile-logo">
                <i class="fas fa-file-contract"></i>
                <h1>SIKAP</h1>
                <p>Sistem Informasi Kearsipan dan Administrasi Perusahaan</p>
            </div>

            <div class="login-header">
                <h2>Selamat Datang</h2>
                <p>Masukkan NIK dan Password Anda untuk mengakses sistem</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-info">
                    {{ session('status') }}
                </div>
            @endif

            <div class="login-form">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- NIK -->
                    <div class="form-group">
                        <label for="nik" class="form-label">NIK</label>
                        <input id="nik" class="form-control @error('nik') is-invalid @enderror" type="text"
                            name="nik" value="{{ old('nik') }}" placeholder="Masukan NIK" required autofocus autocomplete="username">
                        @error('nik')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-input-container">
                            <input id="password" class="form-control @error('password') is-invalid @enderror"
                                type="password" name="password" placeholder="Masukan Password" required autocomplete="current-password">
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="password-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt me-2"></i> Masuk
                        </button>
                    </div>
                </form>
            </div>

            <div class="login-footer">
                <p>SIKAP </p>
                <p>Sistem Informasi Kearsipan dan Administrasi Perusahaan.</p>
                <p>&copy; {{ date('Y') }} Rev.01 </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordEye = document.getElementById('password-eye');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordEye.classList.remove('fa-eye');
                passwordEye.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordEye.classList.remove('fa-eye-slash');
                passwordEye.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>