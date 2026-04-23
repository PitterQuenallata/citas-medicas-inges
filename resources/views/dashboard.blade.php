<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Médico Cristianos Solidarios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
</head>
<body class="bg-light">
    <div class="container-fluid" style="min-height: 100vh;">
        <!-- Header superior -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-2">
            <div class="container-fluid">
                <span class="navbar-brand fw-bold text-primary">Médico Cristianos Solidarios</span>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nombre.' '.auth()->user()->apellido) }}&background=0D8ABC&color=fff&size=32' class="rounded-circle me-2" width="32" height="32" alt="avatar">
                            <span>{{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li class="dropdown-header text-center">
                                <strong>{{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</strong><br>
                                <small class="text-muted">{{ auth()->user()->email }}</small>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="px-3">
                                    @csrf
                                    <button class="dropdown-item text-danger" type="submit">Cerrar sesión</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="row min-vh-100">
            <!-- Sidebar moderno -->
            <nav class="col-md-2 d-none d-md-flex flex-column p-0 shadow-sm sidebar" style="background: #102542 url('/img/fondo.jpg') no-repeat center center; background-size: cover; color: #fff; min-height: 100vh; position:relative;">
                <div style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(16,37,66,0.92);z-index:1;"></div>
                <div class="d-flex flex-column align-items-center py-4 border-bottom position-relative" style="z-index:2;">
                    <img src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nombre.' '.auth()->user()->apellido) }}&background=0D8ABC&color=fff&size=80' class="rounded-circle mb-2" alt="avatar" width="64" height="64">
                    <div class="fw-bold">{{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</div>
                    <small class="text-secondary">{{ auth()->user()->email }}</small>
                </div>
                <ul class="nav flex-column mt-4 w-100 position-relative" style="z-index:2;">
                    <li class="nav-item">
                        <a class="nav-link text-white active" style="background:#1b3358; border-radius: 30px; margin: 0 10px 8px 10px;" href="#">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" style="border-radius: 30px; margin: 0 10px 8px 10px;" href="#">
                            <i class="bi bi-calendar3 me-2"></i> Citas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" style="border-radius: 30px; margin: 0 10px 8px 10px;" href="#">
                            <i class="bi bi-people me-2"></i> Pacientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" style="border-radius: 30px; margin: 0 10px 8px 10px;" href="#">
                            <i class="bi bi-bar-chart me-2"></i> Reportes
                        </a>
                    </li>
                </ul>
                <!-- Botón de cerrar sesión eliminado del sidebar, ahora en el header -->
            </nav>
            <!-- Contenido principal -->
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-primary text-shadow" style="text-shadow: 1px 1px 8px #bfc9d9;">Dashboard</h2>
                </div>
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-lg h-100" style="background:linear-gradient(135deg,#e3e9f6 60%,#f8fafc 100%);">
                            <div class="card-body d-flex flex-column align-items-center">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mb-3 shadow" style="width:56px;height:56px;font-size:2rem;">
                                    <i class="bi bi-calendar3"></i>
                                </div>
                                <h5 class="card-title">Citas próximas</h5>
                                <p class="card-text text-secondary text-center">Aquí podrás ver tus próximas citas médicas.</p>
                                <a href="#" class="btn btn-primary btn-sm mt-auto">Ver citas</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-lg h-100" style="background:linear-gradient(135deg,#eaf6e3 60%,#f8fafc 100%);">
                            <div class="card-body d-flex flex-column align-items-center">
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mb-3 shadow" style="width:56px;height:56px;font-size:2rem;">
                                    <i class="bi bi-people"></i>
                                </div>
                                <h5 class="card-title">Pacientes</h5>
                                <p class="card-text text-secondary text-center">Gestiona y consulta la información de tus pacientes.</p>
                                <a href="#" class="btn btn-success btn-sm mt-auto">Ver pacientes</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-lg h-100" style="background:linear-gradient(135deg,#f6f3e3 60%,#f8fafc 100%);">
                            <div class="card-body d-flex flex-column align-items-center">
                                <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center mb-3 shadow" style="width:56px;height:56px;font-size:2rem;">
                                    <i class="bi bi-bar-chart"></i>
                                </div>
                                <h5 class="card-title">Reportes</h5>
                                <p class="card-text text-secondary text-center">Visualiza reportes y estadísticas del sistema.</p>
                                <a href="#" class="btn btn-warning btn-sm mt-auto text-dark">Ver reportes</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-lg" style="background:rgba(255,255,255,0.97);">
                    <div class="card-header bg-white fw-bold">Bienvenido, {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</div>
                    <div class="card-body">
                        <p class="mb-0">Este es el panel principal del sistema de gestión de citas médicas. Utiliza el menú lateral para navegar por las diferentes secciones.</p>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Bootstrap JS Bundle (para dropdown) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: '¡Bienvenido!',
            text: 'Has iniciado sesión correctamente en el sistema.',
            icon: 'success',
            confirmButtonColor: '#102542',
            confirmButtonText: 'Aceptar',
            background: '#f8fafc',
        });
    });
</script>
</body>
</html>
