-- Agregar campos para recuperación en tabla usuarios
ALTER TABLE usuarios
ADD COLUMN recovery_token VARCHAR(64) DEFAULT NULL,
ADD COLUMN recovery_expira DATETIME DEFAULT NULL;

-- Crear usuario superadmin
INSERT INTO usuarios (nombre, email, password, rol)
VALUES ('Superadmin', 'liderestrategiadigital@bahariaqua.com', '$2y$10$QwWQwWQwWQwWQwWQwWQwWQwWQwWQwWQwWQwWQwWQwWQwWQwWQwW', 'superadmin');
-- Nota: Reemplaza el hash por el generado con password_hash('###12356789**', PASSWORD_DEFAULT)
