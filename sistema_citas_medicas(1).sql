-- =============================================================================
-- SISTEMA DE CITAS MÉDICAS - Script SQL Completo
-- Generado a partir del Diagrama Entidad-Relación (DER)
-- Compatible con: MySQL 8.0+ / MariaDB 10.5+
-- Autor: Ingeniería de Base de Datos
-- =============================================================================
-- NOTAS GENERALES DE OPTIMIZACIÓN:
--   • Se utiliza BIGINT para todas las PKs/FKs: soporta hasta 9.2 × 10^18 registros,
--     evitando futuros problemas de overflow en entornos de producción.
--   • Los ENUM están definidos con los valores exactos del diagrama para garantizar
--     integridad a nivel de motor sin necesidad de tablas de catálogo extra.
--   • Se crean índices sobre TODAS las claves foráneas para optimizar los JOINs.
--   • ON DELETE RESTRICT se usa donde borrar un padre rompería la integridad
--     funcional (ej: no se puede borrar un médico con citas activas).
--   • ON DELETE CASCADE se usa solo en tablas de unión (N:M) donde el registro
--     hijo no tiene sentido sin el padre.
--   • ON UPDATE CASCADE en todas las FKs para propagar cambios de PK sin error.
-- =============================================================================

-- Asegurar que se usa la codificación correcta para acentos y caracteres especiales
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = 'utf8mb4_unicode_ci';

-- Desactivar restricciones FK temporalmente para evitar errores de orden de creación
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================================================
-- TABLA: permisos
-- Propósito: Catálogo de permisos atómicos del sistema (granularidad fina).
--            Cada permiso representa una acción específica en un módulo.
-- =============================================================================
CREATE TABLE IF NOT EXISTS permisos (
    id_permiso   BIGINT          NOT NULL AUTO_INCREMENT,
    -- VARCHAR(100) UNIQUE: nombres de permiso cortos y descriptivos, deben ser únicos
    -- para evitar duplicación de reglas de acceso. Ej: 'citas.crear', 'pacientes.ver'
    nombre_permiso VARCHAR(100)  NOT NULL,
    -- VARCHAR(255): descripción legible para administradores, longitud moderada suficiente
    descripcion  VARCHAR(255)    NULL,
    -- VARCHAR(50): nombre del módulo al que pertenece el permiso. Facilita filtrado por área.
    modulo       VARCHAR(50)     NOT NULL,
    -- TIMESTAMP con DEFAULT: se registra automáticamente en la inserción
    created_at   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    -- TIMESTAMP con ON UPDATE: se actualiza automáticamente al modificar el registro
    updated_at   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT pk_permisos PRIMARY KEY (id_permiso),
    CONSTRAINT uq_permisos_nombre UNIQUE (nombre_permiso)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Catálogo de permisos atómicos del sistema agrupados por módulo';


-- =============================================================================
-- TABLA: roles
-- Propósito: Agrupa conjuntos de permisos bajo un nombre funcional.
--            Ej: 'Administrador', 'Médico', 'Recepcionista', 'Paciente'.
-- =============================================================================
CREATE TABLE IF NOT EXISTS roles (
    id_rol       BIGINT          NOT NULL AUTO_INCREMENT,
    -- VARCHAR(50) UNIQUE: nombre del rol, debe ser único para evitar ambigüedad
    nombre_rol   VARCHAR(50)     NOT NULL,
    descripcion  VARCHAR(255)    NULL,
    -- ENUM: estado binario operativo del rol; TINYINT sería alternativa más compacta
    -- pero ENUM es más legible y autoexplicativo en consultas
    estado       ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    created_at   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT pk_roles PRIMARY KEY (id_rol),
    CONSTRAINT uq_roles_nombre UNIQUE (nombre_rol)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Roles del sistema. Cada rol agrupa un conjunto de permisos';


-- =============================================================================
-- TABLA: rol_permiso  (tabla de unión N:M entre roles y permisos)
-- Propósito: Relaciona qué permisos tiene asignado cada rol.
--            PK compuesta garantiza unicidad sin necesidad de columna surrogate.
-- =============================================================================
CREATE TABLE IF NOT EXISTS rol_permiso (
    -- PK compuesta: la combinación rol+permiso debe ser única
    id_rol       BIGINT          NOT NULL,
    id_permiso   BIGINT          NOT NULL,

    CONSTRAINT pk_rol_permiso PRIMARY KEY (id_rol, id_permiso),

    -- Si se elimina un rol, sus asignaciones de permisos se eliminan en cascada
    -- (la tabla de unión no tiene sentido sin el padre)
    CONSTRAINT fk_rol_permiso_rol
        FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT fk_rol_permiso_permiso
        FOREIGN KEY (id_permiso) REFERENCES permisos(id_permiso)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Tabla de unión N:M: asigna permisos a roles';

-- Índices para optimizar JOINs en ambas direcciones
CREATE INDEX IF NOT EXISTS idx_rol_permiso_rol      ON rol_permiso (id_rol);
CREATE INDEX IF NOT EXISTS idx_rol_permiso_permiso  ON rol_permiso (id_permiso);


-- =============================================================================
-- TABLA: usuarios
-- Propósito: Entidad central de autenticación y seguridad del sistema.
--            Tanto médicos como pacientes pueden tener un usuario asociado.
-- =============================================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario   BIGINT          NOT NULL AUTO_INCREMENT,
    nombre       VARCHAR(100)    NOT NULL,
    apellido     VARCHAR(100)    NOT NULL,
    -- VARCHAR(150) UNIQUE NOT NULL: el email es el identificador principal de login
    email        VARCHAR(150)    NOT NULL,
    telefono     VARCHAR(20)     NULL,
    -- VARCHAR(255): almacena el HASH de la contraseña (bcrypt/argon2 generan ~60-95 chars)
    -- NUNCA almacenar contraseña en texto plano; 255 da margen para futuros algoritmos
    password     VARCHAR(255)    NOT NULL,
    -- ENUM con tres estados para gestión de acceso granular
    estado       ENUM('activo','inactivo','bloqueado') NOT NULL DEFAULT 'activo',
    -- DATETIME (sin zona horaria): registra el último ingreso exitoso al sistema
    ultimo_login DATETIME        NULL,
    created_at   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT pk_usuarios PRIMARY KEY (id_usuario),
    CONSTRAINT uq_usuarios_email UNIQUE (email)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Tabla de autenticación. Médicos y pacientes se vinculan a un usuario';

-- Índice adicional sobre estado para filtrar usuarios activos/bloqueados eficientemente
CREATE INDEX IF NOT EXISTS idx_usuarios_estado ON usuarios (estado);


-- =============================================================================
-- TABLA: usuario_rol  (tabla de unión N:M entre usuarios y roles)
-- Propósito: Un usuario puede tener múltiples roles (Ej: administrador + médico).
-- =============================================================================
CREATE TABLE IF NOT EXISTS usuario_rol (
    id_usuario   BIGINT          NOT NULL,
    id_rol       BIGINT          NOT NULL,

    CONSTRAINT pk_usuario_rol PRIMARY KEY (id_usuario, id_rol),

    -- CASCADE: si se elimina el usuario, sus asignaciones de rol desaparecen
    CONSTRAINT fk_usuario_rol_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT fk_usuario_rol_rol
        FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
        ON DELETE RESTRICT ON UPDATE CASCADE
        -- RESTRICT en rol: no se puede eliminar un rol que tenga usuarios asignados
        -- (protege contra pérdida accidental de configuración de acceso)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Tabla de unión N:M: asigna roles a usuarios del sistema';

CREATE INDEX IF NOT EXISTS idx_usuario_rol_usuario ON usuario_rol (id_usuario);
CREATE INDEX IF NOT EXISTS idx_usuario_rol_rol     ON usuario_rol (id_rol);


-- =============================================================================
-- TABLA: especialidades
-- Propósito: Catálogo de especialidades médicas disponibles en el sistema.
--            Se crea antes que médicos porque médicos referencia a especialidades.
-- =============================================================================
CREATE TABLE IF NOT EXISTS especialidades (
    id_especialidad     BIGINT          NOT NULL AUTO_INCREMENT,
    -- VARCHAR(100) UNIQUE: nombre de la especialidad debe ser único
    nombre_especialidad VARCHAR(100)    NOT NULL,
    descripcion         VARCHAR(255)    NULL,
    estado              ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    created_at          TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT pk_especialidades PRIMARY KEY (id_especialidad),
    CONSTRAINT uq_especialidades_nombre UNIQUE (nombre_especialidad)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Catálogo de especialidades médicas. Ej: Cardiología, Pediatría, etc.';


-- =============================================================================
-- TABLA: medicos
-- Propósito: Perfil profesional del médico. Vinculado a un usuario del sistema.
--            Separado de usuarios para almacenar datos clínicos específicos.
-- =============================================================================
CREATE TABLE IF NOT EXISTS medicos (
    id_medico            BIGINT          NOT NULL AUTO_INCREMENT,
    -- FK UNIQUE: un usuario solo puede ser médico una vez (relación 1:1 opcional)
    id_usuario           BIGINT          NOT NULL,
    -- VARCHAR(30) UNIQUE: código interno único de identificación del médico en el sistema
    codigo_medico        VARCHAR(30)     NOT NULL,
    nombres              VARCHAR(100)    NOT NULL,
    apellidos            VARCHAR(100)    NOT NULL,
    -- VARCHAR(20): Cédula de Identidad. VARCHAR porque puede tener guiones, letras (RIF, etc.)
    ci                   VARCHAR(20)     NULL,
    telefono             VARCHAR(20)     NULL,
    -- VARCHAR(150): email de contacto profesional (puede diferir del email de usuario)
    email                VARCHAR(150)    NULL,
    -- VARCHAR(50) UNIQUE: número de matrícula del colegio médico, debe ser único
    matricula_profesional VARCHAR(50)    NOT NULL,
    estado               ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    created_at           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT pk_medicos PRIMARY KEY (id_medico),
    CONSTRAINT uq_medicos_usuario   UNIQUE (id_usuario),
    CONSTRAINT uq_medicos_codigo    UNIQUE (codigo_medico),
    CONSTRAINT uq_medicos_matricula UNIQUE (matricula_profesional),

    -- RESTRICT: no se puede eliminar un usuario que sea médico activo
    CONSTRAINT fk_medicos_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Perfil profesional del médico. Vinculado 1:1 con la tabla usuarios';

CREATE INDEX IF NOT EXISTS idx_medicos_usuario ON medicos (id_usuario);
CREATE INDEX IF NOT EXISTS idx_medicos_estado  ON medicos (estado);


-- =============================================================================
-- TABLA: medico_especialidad  (tabla de unión N:M entre médicos y especialidades)
-- Propósito: Un médico puede tener múltiples especialidades.
-- =============================================================================
CREATE TABLE IF NOT EXISTS medico_especialidad (
    id_medico       BIGINT          NOT NULL,
    id_especialidad BIGINT          NOT NULL,

    CONSTRAINT pk_medico_especialidad PRIMARY KEY (id_medico, id_especialidad),

    CONSTRAINT fk_me_medico
        FOREIGN KEY (id_medico) REFERENCES medicos(id_medico)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT fk_me_especialidad
        FOREIGN KEY (id_especialidad) REFERENCES especialidades(id_especialidad)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Tabla de unión N:M: asigna especialidades a médicos';

CREATE INDEX IF NOT EXISTS idx_me_medico       ON medico_especialidad (id_medico);
CREATE INDEX IF NOT EXISTS idx_me_especialidad ON medico_especialidad (id_especialidad);


-- =============================================================================
-- TABLA: pacientes
-- Propósito: Información clínica y demográfica del paciente.
--            La vinculación a usuarios es opcional (NULL): un paciente puede existir
--            sin cuenta de usuario (registrado por recepción).
-- =============================================================================
CREATE TABLE IF NOT EXISTS pacientes (
    id_paciente                  BIGINT          NOT NULL AUTO_INCREMENT,
    -- FK NULL UNIQUE: paciente puede no tener usuario, pero si lo tiene, es exclusivo
    id_usuario                   BIGINT          NULL,
    -- VARCHAR(30) UNIQUE: código interno único del paciente (Ej: PAC-0001)
    codigo_paciente              VARCHAR(30)     NOT NULL,
    nombres                      VARCHAR(100)    NOT NULL,
    apellidos                    VARCHAR(100)    NOT NULL,
    -- DATE: solo fecha de nacimiento, no hora (más eficiente que DATETIME)
    fecha_nacimiento             DATE            NULL,
    sexo                         ENUM('masculino','femenino','otro') NULL,
    -- VARCHAR(20): CI puede incluir guiones y letras
    ci                           VARCHAR(20)     NULL,
    -- VARCHAR(255): dirección completa
    direccion                    VARCHAR(255)    NULL,
    telefono                     VARCHAR(20)     NULL,
    email                        VARCHAR(150)    NULL,
    -- VARCHAR(10): grupo sanguíneo + factor Rh. Ej: 'A+', 'O-', 'AB+'
    grupo_sanguineo              VARCHAR(10)     NULL,
    contacto_emergencia_nombre   VARCHAR(150)    NULL,
    contacto_emergencia_telefono VARCHAR(20)     NULL,
    -- TEXT: contenido variable y potencialmente extenso (lista de alergias)
    alergias                     TEXT            NULL,
    -- TEXT: notas clínicas generales, sin límite fijo
    observaciones_generales      TEXT            NULL,
    estado                       ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    created_at                   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT pk_pacientes PRIMARY KEY (id_paciente),
    CONSTRAINT uq_pacientes_usuario  UNIQUE (id_usuario),
    CONSTRAINT uq_pacientes_codigo   UNIQUE (codigo_paciente),

    -- SET NULL: si se elimina el usuario, el paciente permanece pero sin cuenta
    CONSTRAINT fk_pacientes_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Ficha clínica y demográfica del paciente. Puede existir sin usuario del sistema';

CREATE INDEX IF NOT EXISTS idx_pacientes_usuario ON pacientes (id_usuario);
CREATE INDEX IF NOT EXISTS idx_pacientes_estado  ON pacientes (estado);
-- Índice para búsqueda frecuente por apellido/nombre
CREATE INDEX IF NOT EXISTS idx_pacientes_nombre  ON pacientes (apellidos, nombres);


-- =============================================================================
-- TABLA: horarios_medicos
-- Propósito: Define los horarios disponibles de cada médico por día de la semana.
--            Un médico puede tener múltiples horarios (mañana/tarde en distintos días).
-- =============================================================================
CREATE TABLE IF NOT EXISTS horarios_medicos (
    id_horario            BIGINT          NOT NULL AUTO_INCREMENT,
    id_medico             BIGINT          NOT NULL,
    -- TINYINT: día de la semana (1=Lunes ... 7=Domingo). Ocupa 1 byte vs 4 de INT.
    dia_semana            TINYINT         NOT NULL COMMENT '1=Lunes, 2=Martes, ... 7=Domingo',
    -- TIME: solo hora de inicio y fin, sin fecha (eficiente y semánticamente correcto)
    hora_inicio           TIME            NOT NULL,
    hora_fin              TIME            NOT NULL,
    -- INT: duración en minutos del slot de cita. Ej: 20, 30, 45, 60
    duracion_cita_minutos INT             NOT NULL DEFAULT 30,
    -- BOOLEAN (TINYINT(1) internamente): activar/desactivar horario sin eliminarlo
    activo                BOOLEAN         NOT NULL DEFAULT TRUE,
    created_at            TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at            TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT pk_horarios_medicos PRIMARY KEY (id_horario),
    -- RESTRICT: no se puede eliminar un médico que tiene horarios configurados
    CONSTRAINT fk_horarios_medico
        FOREIGN KEY (id_medico) REFERENCES medicos(id_medico)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    -- Validación de integridad: la hora de fin debe ser mayor a la de inicio
    CONSTRAINT chk_horario_horas CHECK (hora_fin > hora_inicio),
    -- Validación: día de semana entre 1 y 7
    CONSTRAINT chk_dia_semana CHECK (dia_semana BETWEEN 1 AND 7),
    -- Validación: duración mínima de 5 minutos y máxima de 240 (4 horas)
    CONSTRAINT chk_duracion CHECK (duracion_cita_minutos BETWEEN 5 AND 240)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Horarios semanales de disponibilidad de cada médico para atención';

CREATE INDEX IF NOT EXISTS idx_horarios_medico    ON horarios_medicos (id_medico);
-- Índice compuesto para consulta frecuente: "¿qué horarios tiene el médico X el día Y?"
CREATE INDEX IF NOT EXISTS idx_horarios_medico_dia ON horarios_medicos (id_medico, dia_semana);


-- =============================================================================
-- TABLA: bloqueos_medicos
-- Propósito: Registra bloqueos de agenda del médico (vacaciones, eventos, etc.)
--            para fechas o rangos de horas específicos. Impide agendar citas.
-- =============================================================================
CREATE TABLE IF NOT EXISTS bloqueos_medicos (
    id_bloqueo   BIGINT          NOT NULL AUTO_INCREMENT,
    id_medico    BIGINT          NOT NULL,
    -- DATE: fecha específica del bloqueo (sin hora)
    fecha        DATE            NOT NULL,
    -- TIME NULL: si hora_inicio/fin son NULL significa bloqueo de día completo
    hora_inicio  TIME            NULL,
    hora_fin     TIME            NULL,
    -- VARCHAR(255): razón del bloqueo. Ej: 'Congreso médico', 'Vacaciones'
    motivo       VARCHAR(255)    NULL,
    created_at   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT pk_bloqueos_medicos PRIMARY KEY (id_bloqueo),

    CONSTRAINT fk_bloqueos_medico
        FOREIGN KEY (id_medico) REFERENCES medicos(id_medico)
        ON DELETE CASCADE ON UPDATE CASCADE,

    -- Si se especifican horas, fin debe ser mayor a inicio
    CONSTRAINT chk_bloqueo_horas CHECK (
        hora_inicio IS NULL OR hora_fin IS NULL OR hora_fin > hora_inicio
    )
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Bloqueos de agenda médica por fecha/hora. Impide programar citas en ese período';

CREATE INDEX IF NOT EXISTS idx_bloqueos_medico       ON bloqueos_medicos (id_medico);
-- Índice compuesto para verificar disponibilidad: médico + fecha
CREATE INDEX IF NOT EXISTS idx_bloqueos_medico_fecha ON bloqueos_medicos (id_medico, fecha);


-- =============================================================================
-- TABLA: citas
-- Propósito: Entidad central del sistema. Registra cada cita médica programada.
--            Relaciona paciente, médico, fecha/hora y estado del turno.
-- =============================================================================
CREATE TABLE IF NOT EXISTS citas (
    id_cita                    BIGINT          NOT NULL AUTO_INCREMENT,
    -- VARCHAR(30) UNIQUE: código alfanumérico legible por humanos. Ej: 'CIT-20240615-0001'
    codigo_cita                VARCHAR(30)     NOT NULL,
    id_paciente                BIGINT          NOT NULL,
    id_medico                  BIGINT          NOT NULL,
    -- FK al usuario que registró la cita (puede ser recepcionista o el propio paciente)
    id_usuario_registra        BIGINT          NOT NULL,
    -- DATE: solo fecha de la cita (la hora se almacena en hora_inicio/fin)
    fecha_cita                 DATE            NOT NULL,
    hora_inicio                TIME            NOT NULL,
    hora_fin                   TIME            NOT NULL,
    -- TEXT: motivo de consulta libre (puede ser extenso)
    motivo_consulta            TEXT            NULL,
    -- ENUM con todos los estados del ciclo de vida de la cita
    estado_cita                ENUM(
                                   'pendiente',
                                   'confirmada',
                                   'atendida',
                                   'cancelada',
                                   'reprogramada',
                                   'no_asistio'
                               ) NOT NULL DEFAULT 'pendiente',
    observaciones              TEXT            NULL,
    -- DATETIME: momento exacto de la cancelación (con hora para auditoría)
    fecha_cancelacion          DATETIME        NULL,
    motivo_cancelacion         VARCHAR(255)    NULL,
    -- Auto-referencia NULL: si esta cita fue reprogramada, apunta a la cita original
    id_cita_reprogramada_desde BIGINT          NULL,
    created_at                 TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                 TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT pk_citas PRIMARY KEY (id_cita),
    CONSTRAINT uq_citas_codigo UNIQUE (codigo_cita),

    -- RESTRICT: no se puede eliminar un paciente/médico con citas registradas
    CONSTRAINT fk_citas_paciente
        FOREIGN KEY (id_paciente) REFERENCES pacientes(id_paciente)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT fk_citas_medico
        FOREIGN KEY (id_medico) REFERENCES medicos(id_medico)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT fk_citas_usuario_registra
        FOREIGN KEY (id_usuario_registra) REFERENCES usuarios(id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    -- SET NULL: si se elimina la cita origen, la referencia se limpia (no bloquea)
    CONSTRAINT fk_citas_reprogramada_desde
        FOREIGN KEY (id_cita_reprogramada_desde) REFERENCES citas(id_cita)
        ON DELETE SET NULL ON UPDATE CASCADE,

    CONSTRAINT chk_citas_horas CHECK (hora_fin > hora_inicio)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Tabla central de citas médicas. Gestiona todo el ciclo de vida del turno';

-- Índices sobre todas las FKs
CREATE INDEX IF NOT EXISTS idx_citas_paciente            ON citas (id_paciente);
CREATE INDEX IF NOT EXISTS idx_citas_medico              ON citas (id_medico);
CREATE INDEX IF NOT EXISTS idx_citas_usuario_registra    ON citas (id_usuario_registra);
CREATE INDEX IF NOT EXISTS idx_citas_reprogramada_desde  ON citas (id_cita_reprogramada_desde);
-- Índice compuesto crítico: buscar agenda del médico por fecha (consulta muy frecuente)
CREATE INDEX IF NOT EXISTS idx_citas_medico_fecha        ON citas (id_medico, fecha_cita);
-- Índice para filtrar por estado (reportes operativos diarios)
CREATE INDEX IF NOT EXISTS idx_citas_estado              ON citas (estado_cita);
-- Índice para el historial de citas de un paciente
CREATE INDEX IF NOT EXISTS idx_citas_paciente_fecha      ON citas (id_paciente, fecha_cita);


-- =============================================================================
-- TABLA: consultas_medicas
-- Propósito: Registro clínico detallado generado durante la atención de una cita.
--            Relación 1:1 con citas (una cita genera máximo una consulta).
--            Contiene signos vitales, diagnóstico, tratamiento y receta.
-- =============================================================================
CREATE TABLE IF NOT EXISTS consultas_medicas (
    id_consulta          BIGINT          NOT NULL AUTO_INCREMENT,
    -- FK UNIQUE: garantiza la relación 1:1 con citas
    id_cita              BIGINT          NOT NULL,
    id_paciente          BIGINT          NOT NULL,
    id_medico            BIGINT          NOT NULL,
    -- DATETIME: momento exacto del inicio de la consulta (fecha + hora)
    fecha_consulta       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    motivo_consulta      TEXT            NULL,
    -- TEXT: campos de historia clínica libre, sin límite de longitud
    sintomas             TEXT            NULL,
    diagnostico          TEXT            NULL,
    tratamiento          TEXT            NULL,
    receta               TEXT            NULL,
    observaciones_medicas TEXT           NULL,
    -- DECIMAL(5,2): hasta 999.99 kg. Más preciso que FLOAT para valores médicos
    peso                 DECIMAL(5,2)    NULL COMMENT 'Peso en kilogramos. Ej: 72.50',
    -- DECIMAL(5,2): hasta 999.99 cm. Ej: 175.00
    talla                DECIMAL(5,2)    NULL COMMENT 'Talla en centímetros. Ej: 175.00',
    -- VARCHAR(20): formato libre "120/80" o "120/80 mmHg"
    presion_arterial     VARCHAR(20)     NULL COMMENT 'Formato: sistólica/diastólica. Ej: 120/80',
    -- DECIMAL(4,2): hasta 99.99°C. Suficiente para temperatura corporal humana
    temperatura          DECIMAL(4,2)    NULL COMMENT 'Temperatura en grados Celsius. Ej: 36.80',
    created_at           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT pk_consultas_medicas PRIMARY KEY (id_consulta),
    -- UNIQUE en id_cita: garantiza la relación 1:1 (una cita → una consulta)
    CONSTRAINT uq_consultas_cita UNIQUE (id_cita),

    -- CASCADE: si se elimina la cita, la consulta asociada también se elimina
    CONSTRAINT fk_consultas_cita
        FOREIGN KEY (id_cita) REFERENCES citas(id_cita)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT fk_consultas_paciente
        FOREIGN KEY (id_paciente) REFERENCES pacientes(id_paciente)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT fk_consultas_medico
        FOREIGN KEY (id_medico) REFERENCES medicos(id_medico)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Historia clínica detallada de la consulta. Relación 1:1 con citas';

CREATE INDEX IF NOT EXISTS idx_consultas_cita      ON consultas_medicas (id_cita);
CREATE INDEX IF NOT EXISTS idx_consultas_paciente  ON consultas_medicas (id_paciente);
CREATE INDEX IF NOT EXISTS idx_consultas_medico    ON consultas_medicas (id_medico);
-- Índice para buscar historial clínico de un paciente por fecha
CREATE INDEX IF NOT EXISTS idx_consultas_pac_fecha ON consultas_medicas (id_paciente, fecha_consulta);


-- =============================================================================
-- TABLA: notificaciones
-- Propósito: Registro de todas las notificaciones enviadas a los pacientes
--            (recordatorios, confirmaciones, cancelaciones) por cualquier canal.
--            Permite auditoría y reintento de envíos fallidos.
-- =============================================================================
CREATE TABLE IF NOT EXISTS notificaciones (
    id_notificacion   BIGINT          NOT NULL AUTO_INCREMENT,
    id_cita           BIGINT          NOT NULL,
    id_paciente       BIGINT          NOT NULL,
    -- ENUM: tipo funcional de la notificación
    tipo_notificacion ENUM('recordatorio','confirmacion','cancelacion') NOT NULL,
    -- ENUM: canal de envío utilizado
    canal             ENUM('email','sms','whatsapp','sistema')          NOT NULL,
    -- TEXT: cuerpo del mensaje enviado (puede ser largo para email)
    mensaje           TEXT            NOT NULL,
    -- DATETIME: momento programado o real del envío
    fecha_envio       DATETIME        NOT NULL,
    -- ENUM: estado del proceso de entrega
    estado_envio      ENUM('pendiente','enviado','fallido')             NOT NULL DEFAULT 'pendiente',
    created_at        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT pk_notificaciones PRIMARY KEY (id_notificacion),

    -- CASCADE: si se elimina la cita, sus notificaciones también se eliminan
    CONSTRAINT fk_notificaciones_cita
        FOREIGN KEY (id_cita) REFERENCES citas(id_cita)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT fk_notificaciones_paciente
        FOREIGN KEY (id_paciente) REFERENCES pacientes(id_paciente)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Registro de notificaciones enviadas al paciente por distintos canales';

CREATE INDEX IF NOT EXISTS idx_notif_cita          ON notificaciones (id_cita);
CREATE INDEX IF NOT EXISTS idx_notif_paciente      ON notificaciones (id_paciente);
-- Índice para consultar notificaciones pendientes de reenvío (jobs de cola)
CREATE INDEX IF NOT EXISTS idx_notif_estado_canal  ON notificaciones (estado_envio, canal);
-- Índice para buscar notificaciones por rango de fecha (reportes y auditoría)
CREATE INDEX IF NOT EXISTS idx_notif_fecha         ON notificaciones (fecha_envio);


-- =============================================================================
-- REACTIVAR RESTRICCIONES DE CLAVES FORÁNEAS
-- =============================================================================
SET FOREIGN_KEY_CHECKS = 1;


-- =============================================================================
-- RESUMEN DEL ESQUEMA GENERADO
-- =============================================================================
-- Tablas creadas (14 en total):
--   1. permisos              - Catálogo de permisos atómicos
--   2. roles                 - Roles del sistema
--   3. rol_permiso           - Unión N:M roles ↔ permisos
--   4. usuarios              - Autenticación y acceso al sistema
--   5. usuario_rol           - Unión N:M usuarios ↔ roles
--   6. especialidades        - Catálogo de especialidades médicas
--   7. medicos               - Perfil profesional del médico
--   8. medico_especialidad   - Unión N:M médicos ↔ especialidades
--   9. horarios_medicos      - Disponibilidad semanal del médico
--  10. bloqueos_medicos      - Bloqueos de agenda del médico
--  11. pacientes             - Ficha clínica y demográfica del paciente
--  12. citas                 - Gestión de turnos médicos (tabla central)
--  13. consultas_medicas     - Historia clínica de la consulta (1:1 con citas)
--  14. notificaciones        - Registro de comunicaciones al paciente
--
-- Índices creados: 28 índices sobre FKs y campos de búsqueda frecuente
-- Constraints CHECK aplicados: 5 validaciones de integridad de dominio
-- Motor: InnoDB (soporte completo de FK, ACID, row-level locking)
-- Charset: utf8mb4 + utf8mb4_unicode_ci (soporte completo Unicode y emojis)
-- =============================================================================
