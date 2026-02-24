-- =====================================================================
-- Migración: Cambio obligatorio de contraseña en primer login
-- Fecha: 24 de febrero de 2026
-- Descripción: Agrega campos para gestionar el primer login y tracking 
--              de cambios de contraseña de usuarios
-- =====================================================================

-- 1. Agregar campo 'estado' (usado en código pero faltaba en BD)
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS estado ENUM('activo','inactivo') DEFAULT 'activo' 
AFTER rol;

-- 2. Agregar campo que indica si es el primer login del usuario
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS primer_login TINYINT(1) DEFAULT 1 
AFTER password;

-- 3. Agregar campo para registrar la fecha del último cambio de contraseña
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS ultimo_cambio_password DATETIME NULL 
AFTER primer_login;

-- 4. Actualizar usuarios existentes para que no se les fuerce el cambio
UPDATE usuarios 
SET primer_login = 0, 
    estado = 'activo' 
WHERE primer_login IS NULL OR estado IS NULL;

-- =====================================================================
-- Notas:
-- - Los usuarios existentes NO serán forzados a cambiar contraseña
-- - Los nuevos usuarios (creados después de esta migración) tendrán 
--   primer_login = 1 y deberán cambiar su contraseña temporal
-- - El campo estado ahora está sincronizado con el código
-- =====================================================================
