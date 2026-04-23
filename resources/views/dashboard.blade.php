<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Médico Cristianos Solidarios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row min-vh-100">
            <!-- Sidebar moderno -->
            <nav class="col-md-2 d-none d-md-flex flex-column bg-dark text-white p-0 shadow-sm sidebar">
                <div class="d-flex flex-column align-items-center py-4 border-bottom">
                    <img src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nombre.' '.auth()->user()->apellido) }}&background=0D8ABC&color=fff&size=80' class="rounded-circle mb-2" alt="avatar" width="64" height="64">
                    <div class="fw-bold">{{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</div>
                    <small class="text-secondary">{{ auth()->user()->email }}</small>
                </div>
                <ul class="nav flex-column mt-4 w-100">
                    <li class="nav-item">
                        <a class="nav-link text-white active bg-primary rounded-pill mx-2 my-1" href="#">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white mx-2 my-1" href="#">
                            <i class="bi bi-calendar3 me-2"></i> Citas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white mx-2 my-1" href="#">
                            <i class="bi bi-people me-2"></i> Pacientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white mx-2 my-1" href="#">
                            <i class="bi bi-bar-chart me-2"></i> Reportes
                        </a>
                    </li>
                </ul>
                <form method="POST" action="{{ route('logout') }}" class="mt-auto mb-4 w-100 px-3">
                    @csrf
                    <button class="btn btn-outline-light w-100" type="submit">Cerrar sesión</button>
                </form>
            </nav>
            <!-- Contenido principal -->
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold">Dashboard</h2>
                </div>
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title">Citas próximas</h5>
                                <p class="card-text text-secondary">Aquí podrás ver tus próximas citas médicas.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title">Pacientes</h5>
                                <p class="card-text text-secondary">Gestiona y consulta la información de tus pacientes.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title">Reportes</h5>
                                <p class="card-text text-secondary">Visualiza reportes y estadísticas del sistema.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold">Bienvenido, {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</div>
                    <div class="card-body">
                        <p class="mb-0">Este es el panel principal del sistema de gestión de citas médicas. Utiliza el menú lateral para navegar por las diferentes secciones.</p>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</body>
</html>
