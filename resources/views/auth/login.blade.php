<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Médico Cristianos Solidarios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body style="background: url('/img/fondo.jpg') no-repeat center center fixed; background-size: cover;">
    <div class="container d-flex align-items-center justify-content-center min-vh-100" style="backdrop-filter: blur(2px);">
        <div class="row w-100 justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-lg border-0" style="background:rgba(255,255,255,0.97);">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="https://ui-avatars.com/api/?name=MCS&background=0D8ABC&color=fff&size=80" class="rounded-circle shadow-sm mb-2" alt="avatar" width="64" height="64">
                            <h4 class="fw-bold mb-0">Iniciar Sesión</h4>
                            <small class="text-secondary">Médico Cristianos Solidarios</small>
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email" value="{{ old('email') }}" required autofocus>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">Entrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
