<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Médico Cristianos Solidarios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Médico Cristianos Solidarios</a>
            <form method="POST" action="{{ route('logout') }}" class="d-flex ms-auto">
                @csrf
                <button class="btn btn-outline-light" type="submit">Cerrar sesión</button>
            </form>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row" style="min-height: 90vh;">
            <!-- Menú lateral -->
            <nav class="col-md-2 d-none d-md-block bg-white border-end sidebar py-4">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2">
                            <a class="nav-link active" href="#">
                                <span class="me-2">🏠</span> Dashboard
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link" href="#">
                                <span class="me-2">📅</span> Citas
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link" href="#">
                                <span class="me-2">👨‍⚕️</span> Pacientes
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link" href="#">
                                <span class="me-2">📊</span> Reportes
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <!-- Contenido principal -->
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Bienvenido, {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</h4>
                    </div>
                    <div class="card-body">
                        <p>Este es el panel principal del sistema de gestión de citas médicas.</p>
                        <ul>
                            <li>Acceso rápido a tus citas</li>
                            <li>Gestión de pacientes</li>
                            <li>Reportes y más...</li>
                        </ul>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
