-- MIGRACIÓN: Agregar columna de notificaciones
ALTER TABLE configuracion ADD COLUMN IF NOT EXISTS notificaciones_ganado TINYINT(1) DEFAULT 0;
