# Sistema Web de Citas Médicas - Guía de Módulos para IA

## 1. Contexto del proyecto

Desarrollar un sistema web para la clínica **"Médicos Cristianos Solidarios"** que permita registrar, administrar y controlar citas médicas.

El problema principal es que actualmente la clínica maneja las citas de forma manual, lo que causa:

- Pérdida de información.
- Duplicidad de horarios.
- Falta de control sobre la disponibilidad de médicos.
- Procesos lentos en recepción.
- Dificultad para consultar información de pacientes.

El sistema debe ayudar a automatizar la gestión de citas, organizar la agenda médica y mejorar la atención a pacientes.

---

## 2. Stack tecnológico del proyecto

> Nota: El documento original propone Laravel + Inertia.js + React, pero para este proyecto se decidió simplificar usando Laravel con vistas Blade y Bootstrap, porque el equipo no tiene mucha experiencia con React/Inertia.

Tecnologías a usar:

- **Backend:** Laravel
- **Frontend:** Blade + Bootstrap
- **Base de datos:** MySQL
- **Autenticación:** Laravel Auth / Laravel Breeze o sistema propio simple
- **Control de roles:** por campo `rol` en usuarios o usando tablas de roles si ya existen
- **Repositorio:** GitHub con ramas por módulo

---

## 3. Roles del sistema

El sistema debe contemplar los siguientes actores:

### Administrador

Tiene control total del sistema.

Puede:

- Gestionar usuarios.
- Gestionar médicos.
- Gestionar pacientes.
- Ver todas las citas.
- Crear, editar, cancelar o reprogramar citas.
- Consultar agenda general.
- Consultar historial clínico básico.

### Médico

Gestiona su propia agenda y atiende citas.

Puede:

- Ver sus citas asignadas.
- Ver su calendario o agenda médica.
- Registrar diagnóstico básico.
- Registrar observaciones de la consulta.
- Consultar historial de pacientes que atiende.

### Paciente

Solicita y consulta citas.

Puede:

- Registrarse o ser registrado por recepción.
- Consultar sus citas.
- Solicitar una cita si el sistema lo permite.
- Ver información básica de sus atenciones.

### Recepcionista

Registra citas manualmente y ayuda en la administración diaria.

Puede:

- Registrar pacientes.
- Crear citas para pacientes.
- Consultar disponibilidad médica.
- Cancelar o reprogramar citas según permisos.

---

## 4. Módulos principales del sistema

El sistema debe dividirse en los siguientes módulos:

1. Autenticación y usuarios.
2. Roles y permisos.
3. Pacientes.
4. Médicos.
5. Especialidades.
6. Horarios médicos.
7. Citas médicas.
8. Agenda médica / calendario.
9. Historial clínico básico.
10. Panel principal / dashboard.

---

# 5. Detalle de módulos

## 5.1 Módulo de autenticación

### Objetivo

Permitir que los usuarios ingresen al sistema de forma segura.

### Funciones

- Login.
- Logout.
- Registro de usuarios, si corresponde.
- Recuperación de contraseña, si se implementa.
- Redirección según rol.

### Reglas

- Solo usuarios registrados pueden entrar al sistema.
- Cada usuario debe tener un rol.
- El sistema debe restringir vistas según el rol.

### Tablas sugeridas

#### users

Campos sugeridos:

- id
- name
- email
- password
- rol
- created_at
- updated_at

Roles sugeridos para el campo `rol`:

- admin
- medico
- paciente
- recepcionista

---

## 5.2 Módulo de pacientes

### Objetivo

Registrar y administrar la información de pacientes.

### Funciones

- Listar pacientes.
- Crear paciente.
- Editar paciente.
- Ver detalle de paciente.
- Buscar paciente por nombre, apellido, CI o teléfono.
- Consultar historial básico del paciente.

### Campos sugeridos

#### pacientes

- id
- user_id, opcional si el paciente también inicia sesión
- nombre
- apellido
- ci
- telefono
- fecha_nacimiento
- direccion
- genero
- created_at
- updated_at

### Vistas sugeridas

- `/pacientes`
- `/pacientes/create`
- `/pacientes/{id}`
- `/pacientes/{id}/edit`

### Reglas

- No registrar pacientes duplicados con el mismo CI.
- El paciente debe existir antes de crear una cita.
- Administrador y recepcionista pueden gestionar pacientes.

---

## 5.3 Módulo de médicos

### Objetivo

Registrar y administrar médicos, sus especialidades y disponibilidad.

### Funciones

- Listar médicos.
- Crear médico.
- Editar médico.
- Ver detalle del médico.
- Asignar especialidad.
- Registrar horarios disponibles.
- Ver agenda del médico.

### Campos sugeridos

#### medicos

- id
- user_id, opcional si el médico inicia sesión
- nombre
- apellido
- ci
- telefono
- especialidad_id
- estado
- created_at
- updated_at

#### especialidades

- id
- nombre
- descripcion
- created_at
- updated_at

### Vistas sugeridas

- `/medicos`
- `/medicos/create`
- `/medicos/{id}`
- `/medicos/{id}/edit`
- `/medicos/{id}/agenda`

### Reglas

- Un médico debe tener una especialidad.
- El médico debe tener horarios disponibles para recibir citas.
- Se recomienda una página de agenda/calendario por médico.

---

## 5.4 Módulo de horarios médicos

### Objetivo

Definir cuándo puede atender cada médico.

### Funciones

- Registrar días de atención.
- Registrar hora de inicio y fin.
- Editar horarios.
- Eliminar horarios.
- Consultar disponibilidad por médico y fecha.

### Campos sugeridos

#### horarios_medicos

- id
- medico_id
- dia_semana
- hora_inicio
- hora_fin
- estado
- created_at
- updated_at

Ejemplo:

- medico_id: 3
- dia_semana: lunes
- hora_inicio: 08:00
- hora_fin: 12:00

### Reglas

- Una cita solo puede crearse dentro del horario disponible del médico.
- No se debe permitir una cita fuera del horario de atención.
- No se debe permitir solapamiento de horarios de un mismo médico.

---

## 5.5 Módulo de citas médicas

### Objetivo

Permitir registrar, editar, cancelar y reprogramar citas médicas.

### Funciones

- Crear cita.
- Editar cita.
- Cancelar cita.
- Reprogramar cita.
- Validar disponibilidad del médico.
- Evitar duplicidad de citas.
- Filtrar citas por médico, paciente, fecha o estado.

### Campos sugeridos

#### citas

- id
- paciente_id
- medico_id
- especialidad_id, opcional si se obtiene desde el médico
- fecha
- hora_inicio
- hora_fin
- motivo
- estado
- created_at
- updated_at

Estados sugeridos:

- pendiente
- confirmada
- atendida
- cancelada
- reprogramada

### Vistas sugeridas

- `/citas`
- `/citas/create`
- `/citas/{id}`
- `/citas/{id}/edit`
- `/citas/{id}/cancelar`
- `/citas/{id}/reprogramar`

### Flujo para crear cita

1. Seleccionar paciente.
2. Seleccionar especialidad, si aplica.
3. Seleccionar médico.
4. Elegir fecha.
5. Mostrar horas disponibles del médico.
6. Seleccionar hora.
7. Validar disponibilidad.
8. Guardar cita.
9. Mostrar mensaje de éxito.

### Reglas importantes

- No permitir dos citas del mismo médico en la misma fecha y hora.
- No permitir citas fuera del horario médico.
- No permitir citas sin paciente.
- No permitir citas sin médico.
- Al cancelar, cambiar estado a `cancelada`, no eliminar necesariamente el registro.
- Al reprogramar, actualizar fecha y hora o crear un registro de cambio si se desea más control.

---

## 5.6 Módulo de agenda médica / calendario

### Objetivo

Visualizar las citas de forma ordenada por médico y fecha.

### Funciones

- Ver agenda general.
- Ver agenda por médico.
- Filtrar por fecha.
- Filtrar por especialidad.
- Mostrar citas del día.
- Mostrar estado de cada cita.

### Vistas sugeridas

- `/agenda`
- `/agenda?medico_id=1&fecha=2026-04-23`
- `/medicos/{id}/agenda`

### Recomendación visual

Crear una vista tipo calendario o tabla diaria.

Ejemplo simple con Bootstrap:

| Hora | Paciente | Médico | Especialidad | Estado |
|---|---|---|---|---|
| 08:00 | Juan Pérez | Dr. Ramos | Medicina General | Confirmada |
| 09:00 | María López | Dr. Ramos | Medicina General | Pendiente |

### Reglas

- La agenda debe mostrar solo las citas activas o también canceladas con distintivo visual.
- El médico debe poder ver principalmente su propia agenda.
- Administrador y recepcionista pueden ver agenda general.

---

## 5.7 Módulo de historial clínico básico

### Objetivo

Registrar información básica de la atención médica.

### Funciones

- Registrar diagnóstico.
- Registrar observaciones.
- Asociar historial a una cita.
- Consultar historial por paciente.

### Campos sugeridos

#### historiales_clinicos

- id
- cita_id
- paciente_id
- medico_id
- diagnostico
- observaciones
- tratamiento
- fecha_atencion
- created_at
- updated_at

### Reglas

- Solo se debe registrar historial cuando la cita fue atendida.
- El historial debe quedar asociado al paciente, médico y cita.
- Médico y administrador pueden consultar historial.

---

## 5.8 Módulo de dashboard

### Objetivo

Mostrar un resumen del sistema según el rol del usuario.

### Para administrador

Mostrar:

- Total de pacientes.
- Total de médicos.
- Total de citas.
- Citas del día.
- Citas pendientes.
- Citas canceladas.

### Para médico

Mostrar:

- Mis citas de hoy.
- Próximas citas.
- Pacientes atendidos.

### Para recepcionista

Mostrar:

- Citas del día.
- Acceso rápido a crear paciente.
- Acceso rápido a crear cita.

### Para paciente

Mostrar:

- Mis próximas citas.
- Mis citas anteriores.

---

# 6. Modelo de datos sugerido

Relaciones principales:

- Un usuario puede ser administrador, médico, paciente o recepcionista.
- Un médico pertenece a una especialidad.
- Un médico tiene muchos horarios médicos.
- Un paciente tiene muchas citas.
- Un médico tiene muchas citas.
- Una cita puede tener un historial clínico.

Modelo lógico:

```text
users
  id
  name
  email
  password
  rol

pacientes
  id
  user_id nullable
  nombre
  apellido
  ci
  telefono
  fecha_nacimiento
  direccion
  genero

especialidades
  id
  nombre
  descripcion

medicos
  id
  user_id nullable
  especialidad_id
  nombre
  apellido
  ci
  telefono
  estado

horarios_medicos
  id
  medico_id
  dia_semana
  hora_inicio
  hora_fin
  estado

citas
  id
  paciente_id
  medico_id
  fecha
  hora_inicio
  hora_fin
  motivo
  estado

historiales_clinicos
  id
  cita_id
  paciente_id
  medico_id
  diagnostico
  observaciones
  tratamiento
  fecha_atencion
```

---

# 7. Reglas de validación principales

## Citas

Antes de guardar una cita, validar:

1. Que el paciente exista.
2. Que el médico exista.
3. Que la fecha no esté vacía.
4. Que la hora no esté vacía.
5. Que la cita esté dentro del horario médico.
6. Que no exista otra cita activa del mismo médico en esa fecha y hora.
7. Que el estado inicial sea `pendiente` o `confirmada`.

Consulta lógica para evitar solapamiento:

```php
Cita::where('medico_id', $medicoId)
    ->where('fecha', $fecha)
    ->where('hora_inicio', $horaInicio)
    ->whereIn('estado', ['pendiente', 'confirmada'])
    ->exists();
```

Si existe, no guardar la cita y mostrar mensaje:

```text
El médico ya tiene una cita registrada en ese horario.
```

---

# 8. División del proyecto por ramas de GitHub

Ramas actuales:

```text
main

develop

feature/citas-pitter
feature/login-walter
feature/medicos-alvaro
feature/pacientes-josue
```

## main

Rama estable/final.

Solo debe recibir cambios cuando el sistema ya esté integrado y probado.

## develop

Rama de integración.

Todas las ramas feature deben unirse primero aquí.

## feature/citas-pitter

Responsable del módulo de citas.

Debe incluir:

- Modelo Cita.
- Migración de citas.
- Controlador de citas.
- Vistas de citas.
- Validación de disponibilidad.
- Cancelación y reprogramación.
- Filtros básicos por médico, paciente y fecha.

## feature/login-walter

Responsable del módulo de autenticación.

Debe incluir:

- Login.
- Logout.
- Registro si aplica.
- Roles de usuario.
- Middleware básico por rol, si se implementa.

## feature/medicos-alvaro

Responsable del módulo de médicos.

Debe incluir:

- Modelo Médico.
- Migración de médicos.
- Especialidades.
- Horarios médicos.
- CRUD de médicos.
- Vista de agenda por médico, si alcanza.

## feature/pacientes-josue

Responsable del módulo de pacientes.

Debe incluir:

- Modelo Paciente.
- Migración de pacientes.
- CRUD de pacientes.
- Búsqueda de pacientes.
- Vista de detalle de paciente.

---

# 9. Orden recomendado de integración

Para evitar conflictos, integrar en este orden:

1. `feature/login-walter` hacia `develop`.
2. `feature/pacientes-josue` hacia `develop`.
3. `feature/medicos-alvaro` hacia `develop`.
4. `feature/citas-pitter` hacia `develop`.
5. Probar todo en `develop`.
6. Corregir errores.
7. Unir `develop` hacia `main`.

Si el módulo login no está terminado, se puede presentar usando acceso simple o sin login completo, pero se debe explicar que la rama existe y que el sistema está preparado para integrar autenticación.

---

# 10. Comandos Git recomendados

## Actualizar develop

```bash
git checkout develop
git pull origin develop
```

## Integrar una rama feature a develop

```bash
git checkout develop
git pull origin develop
git merge origin/feature/pacientes-josue
```

Luego resolver conflictos si aparecen.

Después:

```bash
git add .
git commit -m "Merge modulo pacientes en develop"
git push origin develop
```

## Integrar develop a main

```bash
git checkout main
git pull origin main
git merge develop
git push origin main
```

---

# 11. Criterios mínimos para presentación

El sistema debe demostrar como mínimo:

- Registro o listado de pacientes.
- Registro o listado de médicos.
- Creación de citas.
- Validación para evitar citas duplicadas.
- Visualización de agenda o listado de citas.
- División del trabajo por ramas.
- Integración en `develop`.
- Explicación de que `main` es la versión estable.

---

# 12. Prompt para Claude o Windsurf

Usa este prompt si se quiere pedir a una IA que revise o continúe el sistema:

```text
Actúa como desarrollador Laravel senior.

Estoy desarrollando un sistema web de citas médicas para la clínica "Médicos Cristianos Solidarios".

El proyecto usa Laravel, MySQL, Blade y Bootstrap. No usar React ni Inertia, aunque el documento inicial los mencione, porque el equipo decidió simplificar el frontend.

Necesito que revises o continúes el sistema respetando estos módulos:

1. Autenticación y usuarios.
2. Roles: administrador, médico, paciente y recepcionista.
3. Pacientes.
4. Médicos.
5. Especialidades.
6. Horarios médicos.
7. Citas médicas.
8. Agenda médica/calendario.
9. Historial clínico básico.
10. Dashboard.

Reglas importantes:

- No permitir citas duplicadas para el mismo médico, fecha y hora.
- No permitir citas fuera del horario médico.
- Un paciente debe existir antes de crear una cita.
- Un médico debe tener especialidad.
- Las citas deben tener estados: pendiente, confirmada, atendida, cancelada o reprogramada.
- Al cancelar una cita, cambiar el estado, no eliminar el registro directamente.
- El médico debe poder ver su agenda.
- Administrador y recepcionista pueden gestionar pacientes, médicos y citas.

Ramas del proyecto:

- main: versión estable.
- develop: integración.
- feature/citas-pitter: módulo de citas.
- feature/login-walter: login y roles.
- feature/medicos-alvaro: médicos, especialidades y horarios.
- feature/pacientes-josue: pacientes.

Quiero que analices el código actual y me ayudes a completar el sistema sin romper lo que ya funciona. Antes de modificar, revisa la estructura existente, rutas, controladores, modelos, migraciones y vistas. No rehagas todo desde cero. Trabaja sobre lo existente.

Cuando generes cambios:

- Usa Bootstrap para las vistas.
- Mantén nombres claros.
- Evita código innecesariamente avanzado.
- Explica qué archivos modificarás.
- Si copias archivos de una plantilla, usa comandos cp o copia manual, no reescribas toda la plantilla desde cero.
- Mantén coherencia con las ramas y módulos del equipo.
```

---

# 13. Prompt para revisar si el sistema ya contempla todo

```text
Revisa mi proyecto Laravel de citas médicas y verifica si ya contempla los módulos definidos en este documento:

- Autenticación y usuarios.
- Roles y permisos.
- Pacientes.
- Médicos.
- Especialidades.
- Horarios médicos.
- Citas médicas.
- Agenda médica o calendario.
- Historial clínico básico.
- Dashboard.

También verifica estas reglas:

- No permitir citas duplicadas.
- Validar disponibilidad del médico.
- Respetar horarios médicos.
- Permitir filtrar agenda por médico y fecha.
- Permitir que cada médico vea su agenda.
- Permitir registrar diagnóstico y observaciones en historial clínico.

No modifiques todavía. Primero dame un diagnóstico con:

1. Qué módulos ya están completos.
2. Qué módulos están incompletos.
3. Qué módulos faltan.
4. Qué tablas existen y cuáles faltan.
5. Qué rutas existen y cuáles faltan.
6. Qué controladores existen y cuáles faltan.
7. Qué debería priorizar para la presentación.
```

---

# 14. Alcance que NO se implementará por ahora

No implementar todavía:

- Pagos en línea.
- Integración con WhatsApp.
- Multi-sucursal.
- Reportes estadísticos avanzados.
- Integración con sistemas externos.

Estos puntos pueden mencionarse como mejoras futuras.

---

# 15. Resumen para presentación oral

El sistema se dividió en módulos para que cada integrante trabaje en una rama independiente.

La rama `main` representa la versión estable del sistema, mientras que `develop` se usa para integrar y probar los módulos antes de pasarlos a producción.

Cada estudiante tiene una rama feature:

- Login y roles.
- Pacientes.
- Médicos.
- Citas.

El módulo principal es citas, porque conecta pacientes, médicos, horarios y agenda. La regla más importante es evitar que un médico tenga dos citas en el mismo horario y asegurar que las citas respeten la disponibilidad médica.

