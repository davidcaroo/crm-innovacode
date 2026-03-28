<?php
// models/Reporte.php
require_once __DIR__ . '/BaseModel.php';

class Reporte extends BaseModel
{
    /**
     * Obtiene el resumen de ventas (monto total) por mes para el año actual
     */
    public function ventasMensuales($usuario_id = null)
    {
        $sql = "SELECT MONTH(fecha) as mes, SUM(monto) as total 
                FROM ventas 
                WHERE YEAR(fecha) = YEAR(CURDATE())";

        $params = [];
        if ($usuario_id) {
            $sql .= " AND usuario_id = ?";
            $params[] = $usuario_id;
        }

        $sql .= " GROUP BY MONTH(fecha) ORDER BY MONTH(fecha) ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Tasa de conversión: Ganados vs Perdidos vs Proceso
     */
    public function conversionRates($usuario_id = null)
    {
        $sql = "SELECT etapa_venta, COUNT(*) as cantidad 
                FROM empresas";

        $params = [];
        if ($usuario_id) {
            $sql .= " WHERE usuario_id = ?";
            $params[] = $usuario_id;
        }

        $sql .= " GROUP BY etapa_venta";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Ranking de vendedores (Ventas totales)
     */
    public function rankingVendedores()
    {
        $sql = "SELECT u.nombre, SUM(v.monto) as total_ventas, COUNT(v.id) as num_operaciones
                FROM ventas v
                JOIN usuarios u ON v.usuario_id = u.id
                GROUP BY v.usuario_id
                ORDER BY total_ventas DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Resumen de actividades por tipo (Trazabilidad)
     */
    public function resumenActividades($usuario_id = null)
    {
        $sql = "SELECT tipo_actividad, COUNT(*) as cantidad 
                FROM trazabilidad";

        $params = [];
        if ($usuario_id) {
            $sql .= " WHERE usuario_id = ?";
            $params[] = $usuario_id;
        }

        $sql .= " GROUP BY tipo_actividad";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Resumen global comercial por usuario (admin/superadmin).
     */
    public function resumenGlobalComercialUsuarios($filtros = [])
    {
        $whereEmp = [];
        $params = [];

        if (!empty($filtros['fecha_inicio'])) {
            $whereEmp[] = "e.creado_en >= ?";
            $params[] = $filtros['fecha_inicio'] . ' 00:00:00';
        }
        if (!empty($filtros['fecha_fin'])) {
            $whereEmp[] = "e.creado_en <= ?";
            $params[] = $filtros['fecha_fin'] . ' 23:59:59';
        }

        $whereEmpSql = empty($whereEmp) ? '' : (' AND ' . implode(' AND ', $whereEmp));

        $sql = "SELECT
                    u.id AS usuario_id,
                    u.nombre AS usuario,
                    u.email,
                    u.rol,
                    SUM(CASE WHEN e.etapa_venta = 'contactado' AND UPPER(TRIM(COALESCE(e.aplica, ''))) = 'SI' THEN 1 ELSE 0 END) AS gestiones_realizadas,
                    SUM(CASE WHEN e.etapa_venta = 'perdido' THEN 1 ELSE 0 END) AS perdidas,
                    SUM(CASE WHEN e.etapa_venta = 'negociacion' AND COALESCE(tf.tiene_oferta_servicios, 0) = 1 THEN 1 ELSE 0 END) AS negociacion_con_oferta,
                    SUM(CASE WHEN e.etapa_venta = 'contactado' THEN 1 ELSE 0 END) AS contactado_total,
                    SUM(CASE WHEN e.etapa_venta = 'contactado' AND COALESCE(tf.tiene_estudio_necesidades, 0) = 1 THEN 1 ELSE 0 END) AS contactado_con_estudio,
                    SUM(CASE WHEN e.etapa_venta = 'prospectado' THEN 1 ELSE 0 END) AS prospectado
                FROM usuarios u
                LEFT JOIN empresas e
                    ON e.usuario_id = u.id {$whereEmpSql}
                LEFT JOIN (
                    SELECT
                        empresa_id,
                        MAX(CASE WHEN LOWER(tipo_actividad) IN ('estudio_necesidades', 'estudio de necesidades') THEN 1 ELSE 0 END) AS tiene_estudio_necesidades,
                        MAX(CASE WHEN LOWER(tipo_actividad) IN ('oferta_servicio', 'oferta de servicio', 'oferta de servicios') THEN 1 ELSE 0 END) AS tiene_oferta_servicios
                    FROM trazabilidad
                    GROUP BY empresa_id
                ) tf ON tf.empresa_id = e.id
                WHERE u.estado = 'activo'
                GROUP BY u.id, u.nombre, u.email, u.rol
                ORDER BY u.nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Detalle de empresas por usuario para exportación.
     */
    public function detalleGlobalComercialPorUsuario($usuarioId, $filtros = [])
    {
        $where = ["e.usuario_id = ?"];
        $params = [$usuarioId];

        if (!empty($filtros['fecha_inicio'])) {
            $where[] = "e.creado_en >= ?";
            $params[] = $filtros['fecha_inicio'] . ' 00:00:00';
        }
        if (!empty($filtros['fecha_fin'])) {
            $where[] = "e.creado_en <= ?";
            $params[] = $filtros['fecha_fin'] . ' 23:59:59';
        }

        $sql = "SELECT
                    e.id,
                    e.razon_social,
                    e.dpto,
                    e.ciudad,
                    e.correo_comercial,
                    e.aplica,
                    e.etapa_venta,
                    e.creado_en,
                    COALESCE(tf.tiene_estudio_necesidades, 0) AS tiene_estudio_necesidades,
                    COALESCE(tf.tiene_oferta_servicios, 0) AS tiene_oferta_servicios
                FROM empresas e
                LEFT JOIN (
                    SELECT
                        empresa_id,
                        MAX(CASE WHEN LOWER(tipo_actividad) IN ('estudio_necesidades', 'estudio de necesidades') THEN 1 ELSE 0 END) AS tiene_estudio_necesidades,
                        MAX(CASE WHEN LOWER(tipo_actividad) IN ('oferta_servicio', 'oferta de servicio', 'oferta de servicios') THEN 1 ELSE 0 END) AS tiene_oferta_servicios
                    FROM trazabilidad
                    GROUP BY empresa_id
                ) tf ON tf.empresa_id = e.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY e.razon_social ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
