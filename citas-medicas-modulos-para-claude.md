# Contexto del proyecto - Sistema de Citas Medicas

## Estado actual del proyecto

Proyecto Laravel 12 para la clinica "Medicos Cristianos Solidarios".
Rama principal de desarrollo: `develop`.

---

## Stack

- **Laravel 12** + **Blade** (vistas del lado del servidor)
- **TailwindCSS 4** — NO Bootstrap
- **Alpine.js** — interactividad JS
- **Vite** + **pnpm** — compilacion de assets
- **MySQL** via LAMPP
- **Plantilla UI:** Line One Laravel (en `00-lineone-laravel/`)

Layout base: `@extends('layouts.app')` con `@section('content')`

---

## Estructura de modulos implementados

### Funcionales (logica traida de ramas del equipo)
- `dashboard` → `DashboardController@index` → `resources/views/dashboard.blade.php`
- `citas.*` → `CitasController` → `resources/views/citas/`
- `agenda` → `CitasController@agenda` → `resources/views/agenda/index.blade.php`
- `medicos.*` → `MedicoController` → `resources/views/medicos/`
- `pacientes.*` → `PacienteController` → `resources/views/pacientes/` (incluye show)

### En desarrollo (placeholder "en desarrollo")
- `especialidades.*` → `EspecialidadController`
- `horarios.*` → `HorarioController`
- `historial.*` → `HistorialController`
- `usuarios.*` → `UsuariosController`
- `roles.*` → `RolController`
- `permisos.*` → `PermisoController`
- `notificaciones.index` → `NotificacionController` (historial WhatsApp)
- `auditoria.index` → `AuditoriaController`
- `reportes.index` → `ReportesController`

---

## Sidebar organizado en secciones

El sidebar tiene 7 iconos. El panel lateral cambia segun la seccion activa.

```
Dashboard     → directo
Citas         → Lista de Citas / Nueva Cita / Agenda Medica
Medicos       → Lista / Nuevo / Especialidades / Horarios
Pacientes     → Lista / Nuevo / Historial Clinico
Admin         → Usuarios / Roles / Permisos
Notificaciones→ Historial de Envios
Sistema       → Reportes / Auditoria
```

Archivo: `app/Http/View/Composers/SidebarComposer.php`
- Detecta el route name actual y devuelve el menu contextual correspondiente.

---

## Base de datos - tablas principales

```
users                  PK: id (nombre, apellido, email, telefono, estado)
medicos                PK: id_medico, FK: id_usuario → users.id
pacientes              PK: id_paciente, FK: id_usuario → users.id (nullable)
especialidades         PK: id_especialidad
medico_especialidad    pivot
horarios_medicos       PK: id_horario, FK: id_medico
citas                  PK: id_cita, columnas: fecha_cita, hora_inicio, hora_fin,
                       motivo_consulta, estado_cita, id_paciente, id_medico,
                       id_usuario_registra
notificaciones         PK: id_notificacion, estado_envio: pendiente/enviado/fallido
                       canal: whatsapp/email/sms/sistema
consultas_medicas      historial clinico
roles / permisos / usuario_rol / rol_permiso
```

---

## Archivos criticos - NO modificar sin coordinacion

- `resources/views/layouts/app.blade.php`
- `resources/views/components/app-partials/main-sidebar.blade.php`
- `resources/views/components/app-partials/sidebar-panel.blade.php`
- `app/Http/View/Composers/SidebarComposer.php`
- `resources/css/` y `resources/js/`

---

## Ramas git

```
main          produccion
develop       integracion (base para todo)

# Ramas viejas mergeadas (no tocar):
feature/citas-pitter
feature/medicos-alvaro
feature/pacientes-josue
feature/login-walter

# Ramas nuevas (cada compañero crea la suya desde develop):
feature/especialidades-alvaro
feature/horarios-josue
feature/usuarios-walter
feature/notificaciones-pitter
```

---

## Pendientes documentados en codigo

- `resources/views/notificaciones/index.blade.php` tiene los TODOs de WhatsApp API
- Botones "Notificar" en citas del dia y dia anterior (por implementar en `citas/index`)
- Cron/job para recordatorios automaticos 24h antes
