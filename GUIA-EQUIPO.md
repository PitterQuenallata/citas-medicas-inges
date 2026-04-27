# Guia para el equipo - Sistema de Citas Medicas

> Leer esto antes de tocar cualquier cosa.

---

## 1. Stack que estamos usando

| Cosa | Que es | Notas |
|------|--------|-------|
| **Laravel 12** | Backend / rutas / controladores | PHP |
| **Blade** | Vistas (HTML del servidor) | archivos `.blade.php` |
| **TailwindCSS 4** | Estilos | **NO usamos Bootstrap** |
| **Alpine.js** | Interactividad JS (dropdowns, tooltips, etc.) | ya viene en la plantilla |
| **Vite** | Compilador de assets (CSS + JS) | reemplaza webpack |
| **pnpm** | Gestor de paquetes JS | **NO usar npm ni yarn** |
| **MySQL** | Base de datos | via LAMPP |

> **IMPORTANTE: NO Bootstrap.** Si tu IA genera `class="btn btn-primary"` o `class="container row col-md-6"` eso esta MAL. Las clases son de Tailwind.

---

## 2. La plantilla - Line One Laravel

La plantilla se llama **Line One** y esta en:
```
/opt/lampp/htdocs/citas-medicas-inges/00-lineone-laravel/
```

Ahi puedes ver ejemplos de:
- Tablas, formularios, cards, badges con clases de Tailwind
- Como usar Alpine.js para modales, dropdowns
- Componentes ya armados

**Logica para usarla:** copias el HTML de un ejemplo de la plantilla, pegas en tu vista Blade y cambias el contenido. No cambias CSS, no agregas Bootstrap, no agregas estilos inline.

---

## 3. Como esta organizado el proyecto

```
citas-medicas-inges/
├── app/
│   ├── Http/
│   │   ├── Controllers/       ← logica de cada modulo
│   │   └── View/Composers/
│   │       └── SidebarComposer.php   ← NO TOCAR (maneja el sidebar)
│   └── Models/                ← modelos de BD
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php        ← layout principal - NO TOCAR
│   │   ├── components/
│   │   │   └── app-partials/        ← sidebar, header - NO TOCAR
│   │   ├── dashboard.blade.php
│   │   ├── citas/
│   │   ├── medicos/
│   │   ├── pacientes/
│   │   ├── agenda/
│   │   ├── especialidades/   ← EN DESARROLLO (tu modulo puede estar aqui)
│   │   ├── horarios/         ← EN DESARROLLO
│   │   ├── historial/        ← EN DESARROLLO
│   │   ├── usuarios/         ← EN DESARROLLO
│   │   ├── roles/            ← EN DESARROLLO
│   │   ├── permisos/         ← EN DESARROLLO
│   │   ├── notificaciones/   ← EN DESARROLLO
│   │   ├── auditoria/        ← EN DESARROLLO
│   │   └── reportes/         ← EN DESARROLLO
│   ├── css/
│   │   └── app.css           ← NO TOCAR
│   └── js/
│       └── app.js            ← NO TOCAR
├── routes/
│   └── web.php               ← aqui se agregan rutas nuevas
└── database/
    ├── migrations/            ← estructura de la BD
    └── seeders/               ← datos de prueba
```

---

## 4. Como crear una vista correctamente

### Estructura minima de cualquier vista

```blade
@extends('layouts.app')
@section('title', 'Nombre de tu pagina')

@section('content')

{{-- Tu contenido aqui con clases de Tailwind --}}

<div class="card px-4 pb-4 sm:px-5">
    <h3 class="text-base font-medium text-slate-700 py-4">Mi modulo</h3>
    <p class="text-sm text-slate-500">Contenido...</p>
</div>

@endsection
```

### Clases utiles de Tailwind que ya usamos

```html
<!-- Card (contenedor blanco con sombra) -->
<div class="card px-4 pb-4 sm:px-5"> ... </div>

<!-- Badge de estado -->
<span class="badge rounded-full bg-success/10 text-success">Activo</span>
<span class="badge rounded-full bg-error/10 text-error">Inactivo</span>
<span class="badge rounded-full bg-warning/10 text-warning">Pendiente</span>
<span class="badge rounded-full bg-info/10 text-info">Confirmada</span>

<!-- Boton primario -->
<a href="..." class="btn bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus">
    Boton
</a>

<!-- Boton de borde -->
<a href="..." class="btn border border-slate-300 px-4 text-sm hover:bg-slate-100">
    Cancelar
</a>

<!-- Input de formulario -->
<input type="text" name="campo"
    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none" />

<!-- Select -->
<select name="campo"
    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none">
    <option>Opcion</option>
</select>

<!-- Tabla -->
<table class="is-hoverable w-full text-left">
    <thead>
        <tr class="border-y border-transparent border-b-slate-200">
            <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5">
                Columna
            </th>
        </tr>
    </thead>
    <tbody>
        <tr class="border-y border-transparent border-b-slate-200">
            <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-slate-600">
                Dato
            </td>
        </tr>
    </tbody>
</table>
```

### Pagina "en desarrollo" (placeholder rapido)

```blade
@extends('layouts.app')
@section('title', 'Mi Modulo')

@section('content')
<div class="card flex flex-col items-center justify-center py-16 text-center">
    <h2 class="text-xl font-semibold text-slate-700">Mi Modulo</h2>
    <p class="mt-2 text-sm text-slate-400">Este modulo esta en desarrollo</p>
    <span class="mt-4 badge rounded-full bg-warning/10 px-4 py-1.5 text-sm text-warning">En Desarrollo</span>
</div>
@endsection
```

---

## 5. Como agregar una ruta nueva

En `routes/web.php`, dentro del grupo `Route::middleware('auth')->group(...)`:

```php
// Ruta simple
Route::get('mi-modulo', [MiController::class, 'index'])->name('mi-modulo.index');

// CRUD completo
Route::resource('mi-modulo', MiController::class);

// Solo algunos metodos
Route::resource('mi-modulo', MiController::class)->only(['index', 'create', 'store', 'edit', 'update']);
```

---

## 6. Lo que NO debes tocar

| Archivo / Carpeta | Por que no tocarlo |
|-------------------|-------------------|
| `resources/views/layouts/app.blade.php` | Es el layout base, si lo rompes todo se cae |
| `resources/views/components/app-partials/` | sidebar, header - ya configurados |
| `resources/css/` y `resources/js/` | assets de la plantilla compilados |
| `app/Http/View/Composers/SidebarComposer.php` | lo maneja Pitter, coordinar antes |
| `database/migrations/` | no crear migraciones sin coordinarlo |
| `vendor/` | nunca jamas |

---

## 7. Comandos que necesitas

```bash
# Levantar el servidor
php artisan serve

# Compilar assets (SIEMPRE despues de cambiar CSS/JS)
pnpm run build

# Limpiar cache de vistas (si algo no se actualiza)
php artisan view:clear

# Ver todas las rutas
php artisan route:list

# Ejecutar migraciones
php artisan migrate
```

> Usa **pnpm**, no npm. Si usas npm puedes romper el lock file.

---

## 8. Base de datos - tablas principales

```
users              ← usuarios del sistema (login)
medicos            ← datos del medico (FK a users.id)
pacientes          ← datos del paciente
especialidades     ← especialidades medicas
medico_especialidad← pivot medico <-> especialidad
horarios_medicos   ← horarios por medico y dia
citas              ← citas (tabla principal)
notificaciones     ← historial de notificaciones WhatsApp
consultas_medicas  ← historial clinico
roles / permisos / usuario_rol / rol_permiso ← acceso
```

> La tabla de usuarios se llama `users` (no `usuarios`). La PK es `id` (no `id_usuario`).

---

## 9. Git - flujo de trabajo

### Ramas existentes (ya mergeadas, no borrar)
```
feature/citas-pitter
feature/medicos-alvaro
feature/pacientes-josue
feature/login-walter
```

### Como crear tu nueva rama para el modulo nuevo

```bash
# 1. Asegurate de estar en develop actualizado
git checkout develop
git pull origin develop

# 2. Crear tu rama nueva
git checkout -b feature/mi-modulo-minombre

# 3. Trabajar, hacer commits
git add .
git commit -m "agregar vista de mi modulo"

# 4. Subir al remoto
git push origin feature/mi-modulo-minombre
```

### Division de trabajo por secciones

**Pitter (lider)** — Seccion: Citas + Administracion + Notificaciones
| Modulo | Dificultad | Estado |
|--------|-----------|--------|
| Citas (mejorar) | Alta | Funcional base |
| Agenda Medica | Alta | Funcional base |
| Pagos (efectivo + QR futuro) | Alta | Nuevo |
| Usuarios / Roles / Permisos | Media | Placeholder |
| Notificaciones WhatsApp (kapso.ai) | Alta | Placeholder |

**Josue** — Seccion: Pacientes + Sistema (Reportes)
| Modulo | Rama | Nota |
|--------|------|------|
| Pacientes | `feature/pacientes-josue-v2` | Ya tiene SweetAlert, validaciones |
| Historial Clinico | misma rama | Extension de pacientes |
| Reportes | misma rama | Reportes del sistema |

**Alvaro** — Seccion: Medicos (toda la seccion)
| Modulo | Rama | Nota |
|--------|------|------|
| Medicos (corregir) | `feature/medicos-alvaro-v2` | Adaptar a plantilla nueva |
| Especialidades | misma rama | ABM de especialidades |
| Horarios Medicos | misma rama | Horarios por medico y dia |

**Walter** — Seccion: Dashboard + Sistema (Auditoria) + Login
| Modulo | Rama | Nota |
|--------|------|------|
| Login (mantener/mejorar) | `feature/login-dashboard-walter` | Ya lo tiene |
| Dashboard (stats reales) | misma rama | Mejorar con datos reales |
| Auditoria | misma rama | Log de acciones del sistema |

### Resumen rapido

```
Pitter:  Citas + Agenda + Pagos + Usuarios/Roles/Permisos + WhatsApp
Josue:   Pacientes + Historial Clinico + Reportes
Alvaro:  Medicos + Especialidades + Horarios Medicos
Walter:  Login + Dashboard + Auditoria
```

### Convenciones de commits

```bash
# Todo en minusculas, sin acentos, en español, estilo simple
git commit -m "agregar vista de especialidades"
git commit -m "corregir formulario de horarios"
git commit -m "crear controlador de usuarios"
git commit -m "actualizar tabla de notificaciones"
```

---

## 10. Resumen rapido del contexto del proyecto

Si alguien nuevo entra al proyecto, esto es lo minimo que debe saber:

```
Proyecto: Sistema de citas medicas para clinica Medicos Cristianos Solidarios
Framework: Laravel 12 + Blade
Estilos: TailwindCSS 4 (NO Bootstrap)
JS: Alpine.js (ya incluido en la plantilla)
Compilador: Vite con pnpm
Plantilla UI: Line One Laravel (sidebar + header ya integrados)
Layout base: @extends('layouts.app') con @section('content')
BD: MySQL, tabla principal 'users' (PK: id, NO id_usuario)
Rama base: develop
```

---

*Cualquier duda coordinan con Pitter antes de hacer cambios en archivos compartidos.*
