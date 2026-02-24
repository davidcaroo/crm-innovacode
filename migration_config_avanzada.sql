-- ============================================================
-- MIGRACIÓN: Configuración Avanzada SaaS
-- Módulo: Integraciones, Notificaciones In-App, Preferencias
-- Fecha: 2026-02-23
-- ============================================================

-- 1. Tabla de integraciones externas
CREATE TABLE IF NOT EXISTS integraciones (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    slug        VARCHAR(50)  NOT NULL UNIQUE COMMENT 'ej: whatsapp, google_calendar',
    nombre      VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255),
    tipo_auth   ENUM('api_key','oauth','url','none') DEFAULT 'none',
    estado      ENUM('inactiva','activa','error')    DEFAULT 'inactiva',
    campos      TEXT COMMENT 'JSON con los campos configurados (cifrados)',
    error_msg   TEXT         COMMENT 'Último error registrado',
    actualizado_en DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_en   DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabla de notificaciones in-app
CREATE TABLE IF NOT EXISTS notificaciones (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id  INT          NOT NULL,
    tipo        VARCHAR(50)  NOT NULL COMMENT 'ej: venta_ganada, empresa_creada',
    titulo      VARCHAR(150) NOT NULL,
    mensaje     TEXT,
    url_accion  VARCHAR(255) COMMENT 'URL a donde redirigir al hacer click',
    leida       TINYINT(1)   DEFAULT 0,
    creado_en   DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_leida (usuario_id, leida),
    INDEX idx_creado (creado_en)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabla de preferencias de notificación por usuario/rol
CREATE TABLE IF NOT EXISTS notificacion_preferencias (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id  INT          NULL COMMENT 'NULL = preferencia global o por rol',
    rol         VARCHAR(50)  NULL COMMENT 'Si usuario_id es NULL, aplica a este rol',
    evento      VARCHAR(50)  NOT NULL COMMENT 'venta_ganada, empresa_creada, cambio_etapa, credito_aprobado',
    canal_email  TINYINT(1)  DEFAULT 1,
    canal_inapp  TINYINT(1)  DEFAULT 1,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY uq_pref_usuario_evento (usuario_id, evento),
    UNIQUE KEY uq_pref_rol_evento     (rol, evento),
    INDEX idx_evento (evento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEEDS: Integraciones disponibles
-- ============================================================
INSERT IGNORE INTO integraciones (slug, nombre, descripcion, tipo_auth, estado) VALUES
('whatsapp',        'WhatsApp',          'Enviar mensajes vía WhatsApp (wa.me o API)',   'api_key', 'inactiva'),
('google_calendar', 'Google Calendar',   'Sincronizar eventos con Google Calendar',        'oauth',   'inactiva'),
('google_maps',     'Google Maps',       'Autocompletar y visualizar direcciones',          'api_key', 'inactiva'),
('webhook_generico','Webhook Genérico',  'Enviar eventos a una URL externa (n8n, etc.)', 'url',     'inactiva'),
('zapier',          'Zapier',            'Conectar con miles de aplicaciones via Zapier',  'api_key', 'inactiva');

-- ============================================================
-- SEEDS: Preferencias globales por defecto (rol + eventos)
-- ============================================================
INSERT IGNORE INTO notificacion_preferencias (usuario_id, rol, evento, canal_email, canal_inapp) VALUES
(NULL, 'admin',      'venta_ganada',     1, 1),
(NULL, 'admin',      'empresa_creada',   0, 1),
(NULL, 'admin',      'cambio_etapa',     0, 1),
(NULL, 'admin',      'credito_aprobado', 1, 1),
(NULL, 'superadmin', 'venta_ganada',     1, 1),
(NULL, 'superadmin', 'empresa_creada',   1, 1),
(NULL, 'superadmin', 'cambio_etapa',     1, 1),
(NULL, 'superadmin', 'credito_aprobado', 1, 1),
(NULL, 'usuario',    'venta_ganada',     0, 1),
(NULL, 'usuario',    'empresa_creada',   0, 0),
(NULL, 'usuario',    'cambio_etapa',     0, 1),
(NULL, 'usuario',    'credito_aprobado', 0, 1);

-- ============================================================
-- ALTERAR tabla configuracion: agregar campo encryption si no existe
-- (La tabla ya existe con smtp_host, smtp_port, smtp_user, smtp_pass, notificaciones_ganado)
-- ============================================================
ALTER TABLE configuracion
    MODIFY COLUMN smtp_pass VARCHAR(500) NULL COMMENT 'Cifrado AES-256';
