-- Tabla para configuración SMTP
CREATE TABLE IF NOT EXISTS configuracion (
    id INT PRIMARY KEY AUTO_INCREMENT,
    smtp_host VARCHAR(128),
    smtp_port INT,
    smtp_user VARCHAR(128),
    smtp_pass VARCHAR(256),
    notificaciones_ganado TINYINT(1) DEFAULT 0
);
-- Insertar valores iniciales
INSERT INTO configuracion (smtp_host, smtp_port, smtp_user, smtp_pass, notificaciones_ganado) VALUES ('', 0, '', '', 0);
