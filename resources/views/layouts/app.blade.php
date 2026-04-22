<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema Citas Médicas</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
@stack('scripts')
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand">Sistema de Citas Médicas</span>
    </div>
</nav>

<div class="container mt-4">
    @yield('content')
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>