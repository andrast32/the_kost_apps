<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }} | Login</title>

    <link rel="icon" href="{{ asset('UI/utama/assets/images/info-icon-03.png') }}" type="image/x-icon">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('UI/dashboard/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('UI/dashboard/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('UI/dashboard/dist/css/adminlte.min.css') }}">

    <link rel="stylesheet" href="{{ asset('UI/utama/assets/css/login.css') }}">

</head>

<body class="hold-transition login-page">

    <div class="login-box">

        <div class="card card-outline card-primary">

            <div class="card-header text-center">
                <p class="h2 mt-3"><b style="color: #8b0420;">The Kost </b>| Login</p>
            </div>

            <div class="card-body">
                <p class="login-box-msg">Silakan masuk ke akun Anda</p>
                <form method="POST" action="{{ route('login') }}">

                    @csrf

                    <div class="mb-3">

                        <label for="email" class="form-label">Email</label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>

                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus>
                        </div>

                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>

                            <input type="password" name="password" id="password" class="form-control" required>

                            <span class="input-group-text" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="row">

                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>

                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block mb-2">Sign In</button>
                        </div>

                    </div>

                </form>

                <div class="row">

                    <div class="col-8">
                        <p class="mb-1">
                            <a href="{{ route('password.request') }}">Lupa Password</a>
                        </p>
                        <p class="mb-0">
                            <a href="{{ route('register') }}" class="text-center">Daftar di sini</a>
                        </p>
                    </div>

                    <div class="col-4">
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-block mt-2">
                            Kembali
                        </a>
                    </div>

                </div>

            </div>

        </div>


    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function togglePassword() {
            const input = document.getElementById("password");
            const icon = document.getElementById("toggleIcon");
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>

    @if ($errors->any())
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                // Ambil pesan error pertama
                title: '{{ $errors->all()[0] }}',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });
        </script>
    @endif

</body>
</html>
