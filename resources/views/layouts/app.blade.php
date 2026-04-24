<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">Citas Médicas</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @auth
                    <li class="nav-item"><a class="nav-link" href="{{ route('pacientes.index') }}">Pacientes</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('medicos.index') }}">Médicos</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('citas.index') }}">Citas</a></li>
                @endauth
            </ul>

            <ul class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item" type="submit">Salir</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<main class="py-4">
    <div class="container">
        @yield('content')
    </div>
</main>

@if (session('success'))
    <script>
        (function () {
            var message = @json(session('success'));
            var show = function () {
                window.Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: message,
                });
            };

            var wait = function () {
                if (window.Swal && typeof window.Swal.fire === 'function') {
                    show();
                } else {
                    setTimeout(wait, 50);
                }
            };

            wait();
        })();
    </script>
@endif

@if (session('error'))
    <script>
        (function () {
            var message = @json(session('error'));
            var show = function () {
                window.Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                });
            };

            var wait = function () {
                if (window.Swal && typeof window.Swal.fire === 'function') {
                    show();
                } else {
                    setTimeout(wait, 50);
                }
            };

            wait();
        })();
    </script>
@endif

@if ($errors->any() && empty($suppressSwalErrors))
    <script>
        (function () {
            var message = @json($errors->first());
            var show = function () {
                window.Swal.fire('SweetAlert2 is working!', message, 'error');
            };

            var wait = function () {
                if (window.Swal && typeof window.Swal.fire === 'function') {
                    show();
                } else {
                    setTimeout(wait, 50);
                }
            };

            wait();
        })();
    </script>
@endif
</body>
</html>
