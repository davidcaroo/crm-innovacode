-- ============================================================
-- Migración CRM 2026 - Ejecutar en orden
-- ============================================================

-- 1. Nueva tabla ventas (reemplaza ventas_clientes)
-- Relacionada con empresas (no con clientes)
CREATE TABLE IF NOT EXISTS `ventas` (
    `id`         INT           NOT NULL AUTO_INCREMENT,
    `empresa_id` INT           NOT NULL,
    `monto`      DECIMAL(12,2) NOT NULL,
    `fecha`      DATE          NOT NULL,
    `usuario_id` INT           NOT NULL,
    `creado_en`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_venta_empresa`  FOREIGN KEY (`empresa_id`)  REFERENCES `empresas`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_venta_usuario`  FOREIGN KEY (`usuario_id`)  REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Agregar tipo_actividad a trazabilidad (si no existe ya)
ALTER TABLE `trazabilidad`
    ADD COLUMN IF NOT EXISTS `tipo_actividad`
        ENUM('llamada','correo','reunion','visita','nota')
        NOT NULL DEFAULT 'nota'
        AFTER `etapa_venta`;

-- 3. Asegurar que la columna etapa_venta exista en empresas
-- (puede que ya esté; IF NOT EXISTS evita error)
ALTER TABLE `empresas`
    ADD COLUMN IF NOT EXISTS `etapa_venta`
        ENUM('prospectado','contactado','negociacion','ganado','perdido')
        NOT NULL DEFAULT 'prospectado'
        AFTER `actividad_economica`;
