# Sistema de Citas Medicas
### Clinica Medicos Cristianos Solidarios

Sistema web para gestionar citas medicas, agenda, pacientes y medicos.

---

## Stack

- **Laravel 12** + **Blade**
- **TailwindCSS 4** (NO Bootstrap)
- **Alpine.js**
- **Vite** + **pnpm**
- **MySQL**
- **Plantilla UI:** Line One Laravel

---

## Requisitos

- PHP 8.2+
- Composer
- Node.js + pnpm
- MySQL (LAMPP / XAMPP)

---

## Instalacion

```bash
# 1. Clonar el repositorio
git clone https://github.com/PitterQuenallata/citas-medicas-inges.git
cd citas-medicas-inges

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias JS
pnpm install

# 4. Copiar el archivo de entorno
cp .env.example .env

# 5. Generar clave de la app
php artisan key:generate

# 6. Configurar la BD en .env
DB_DATABASE=citas_medicas
DB_USERNAME=root
DB_PASSWORD=

# 7. Crear la base de datos y migrar
php artisan migrate

# 8. (Opcional) Datos de prueba
php artisan db:seed

# 9. Compilar assets
pnpm run build

# 10. Levantar el servidor
php artisan serve
```

Abrir en el navegador: `http://localhost:8000`

---

## Modulos

| Modulo | Estado |
|--------|--------|
| Dashboard | Funcional |
| Citas | Funcional |
| Agenda Medica | Funcional |
| Medicos | Funcional |
| Pacientes | Funcional |
| Especialidades | En desarrollo |
| Horarios Medicos | En desarrollo |
| Historial Clinico | En desarrollo |
| Usuarios / Roles / Permisos | En desarrollo |
| Notificaciones WhatsApp | En desarrollo |
| Reportes | En desarrollo |
| Auditoria | En desarrollo |

---

## Equipo

| Integrante | Rama anterior | Modulo nuevo |
|------------|--------------|--------------|
| Pitter | feature/citas-pitter | feature/notificaciones-pitter |
| Alvaro | feature/medicos-alvaro | feature/especialidades-alvaro |
| Josue | feature/pacientes-josue | feature/horarios-josue |
| Walter | feature/login-walter | feature/usuarios-walter |

---

## Flujo de trabajo

```bash
# Antes de trabajar, siempre actualizar desde develop
git checkout develop
git pull origin develop

# Crear tu rama nueva
git checkout -b feature/mi-modulo-minombre

# Commits en español, minusculas
git commit -m "agregar vista de mi modulo"

# Subir la rama
git push origin feature/mi-modulo-minombre
```

> Ver `GUIA-EQUIPO.md` para instrucciones detalladas de desarrollo.

---

## Comandos utiles

```bash
php artisan view:clear   # limpiar cache de vistas
php artisan route:list   # ver todas las rutas
pnpm run build           # compilar assets
pnpm run dev             # compilar en modo desarrollo (watch)
```
