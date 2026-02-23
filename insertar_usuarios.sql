-- SQL para insertar usuarios en el CRM
-- Ejecuta este script después de crear las tablas con esquema_crm.sql

-- Primero, actualizar el ENUM de rol para incluir 'superadmin'
ALTER TABLE usuarios MODIFY rol ENUM('usuario','admin','superadmin') DEFAULT 'usuario';

-- Insertar superadmin
-- Email: liderestrategiadigital@bahariaqua.com
-- Password: ###12356789**
INSERT INTO usuarios (nombre, email, password, rol)
VALUES ('Superadmin', 'liderestrategiadigital@bahariaqua.com', '$2y$10$9jDN62KD5Ai3IkuYWmt.f.yD2pqwwQNyH8E5NszKS3tMTQaqSoqBm', 'superadmin');

-- Insertar empleados mock para pruebas
-- Email: empleado1@bahariaqua.com, empleado2@bahariaqua.com, empleado3@bahariaqua.com
-- Password para todos: empleado123
INSERT INTO usuarios (nombre, email, password, rol)
VALUES
('Empleado Uno', 'empleado1@bahariaqua.com', '$2y$10$KxvlcAkZEhnv4asLPogne.SNdizGb8sc87jh2XqpkO5pjQC27ZE4q', 'usuario'),
('Empleado Dos', 'empleado2@bahariaqua.com', '$2y$10$ZDfHtlOM6DhQfulWZAjiyOdHDruFRaHIHioz2ZKnc3EhPj5CBOaJO', 'usuario'),
('Empleado Tres', 'empleado3@bahariaqua.com', '$2y$10$luBuudf5.jWmRWj/T.IoI.iXx73Q0uZJwCzEywfa2YdCqwuah1i.W', 'usuario');

-- Nota: Estos hashes son únicos y fueron generados con password_hash() de PHP
