-- Modificar el enum de etapa_venta para añadir 'seguimiento'
ALTER TABLE empresas MODIFY COLUMN etapa_venta enum('prospectado','contactado','negociacion','seguimiento','ganado','perdido') DEFAULT 'prospectado';

-- También actualizar la tabla de trazabilidad
ALTER TABLE trazabilidad MODIFY COLUMN etapa_venta enum('prospectado','contactado','negociacion','seguimiento','ganado','perdido') DEFAULT NULL;