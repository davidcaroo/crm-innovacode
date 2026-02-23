-- Esquema inicial para CRM de trazabilidad de empresas, contactos, etapas y usuarios

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('usuario','admin','superadmin') DEFAULT 'usuario',
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    razon_social VARCHAR(150) NOT NULL,
    dpto VARCHAR(50),
    ciudad VARCHAR(50),
    actividad_economica VARCHAR(100),
    correo_comercial VARCHAR(100),
    aplica VARCHAR(10),
    etapa_venta ENUM('prospectado','contactado','negociacion','ganado','perdido') DEFAULT 'prospectado',
    observaciones TEXT,
    usuario_id INT,
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    cargo VARCHAR(50),
    email VARCHAR(100),
    telefono VARCHAR(30),
    FOREIGN KEY (empresa_id) REFERENCES empresas(id)
);

CREATE TABLE trazabilidad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    usuario_id INT NOT NULL,
    etapa_venta ENUM('prospectado','contactado','negociacion','ganado','perdido'),
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    observaciones TEXT,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Para login seguro, usar password_hash en PHP
-- El admin puede ver todas las empresas/contactos/trazabilidad
-- Los usuarios solo ven sus propias empresas/contactos/trazabilidad
-- El historial de etapas se almacena en trazabilidad
-- La tabla empresas guarda la etapa actual
-- La tabla contactos permite múltiples personas por empresa
-- La tabla usuarios permite login y control de roles
