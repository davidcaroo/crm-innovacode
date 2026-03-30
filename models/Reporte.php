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
        $paramsEmp = [];
        $whereUser = ["u.estado = 'activo'"];
        $paramsUser = [];

        if (!empty($filtros['usuario_id'])) {
            $whereUser[] = "u.id = ?";
            $paramsUser[] = $filtros['usuario_id'];
        }
        if (!empty($filtros['fecha_inicio'])) {
            $whereEmp[] = "e.creado_en >= ?";
            $paramsEmp[] = $filtros['fecha_inicio'] . ' 00:00:00';
        }
        if (!empty($filtros['fecha_fin'])) {
            $whereEmp[] = "e.creado_en <= ?";
            $paramsEmp[] = $filtros['fecha_fin'] . ' 23:59:59';
        }

        $whereEmpSql = empty($whereEmp) ? '' : (' AND ' . implode(' AND ', $whereEmp));
        $whereUserSql = implode(' AND ', $whereUser);

        $sql = "SELECT
                    u.id AS usuario_id,
                    u.nombre AS usuario,
                    u.email,
                    u.rol,
                    
                    -- 1. Investigación: (Acumulativo) Tienen al menos un contacto
                    SUM(CASE WHEN tc.empresa_id IS NOT NULL THEN 1 ELSE 0 END) AS investigacion,
                    
                    -- 2. Contacto Efectivo: Etapa 'contactado', NO aplican, y NO tienen estudio
                    SUM(CASE WHEN LOWER(TRIM(e.etapa_venta)) = 'contactado' AND UPPER(TRIM(COALESCE(e.aplica, ''))) != 'SI' AND COALESCE(tf.tiene_estudio_necesidades, 0) = 0 THEN 1 ELSE 0 END) AS contacto_efectivo,
                    
                    -- 3. Contacto Interesado: Etapa 'contactado', SI aplican, pero NO tienen estudio de necesidades
                    SUM(CASE WHEN LOWER(TRIM(e.etapa_venta)) = 'contactado' AND UPPER(TRIM(COALESCE(e.aplica, ''))) = 'SI' AND COALESCE(tf.tiene_estudio_necesidades, 0) = 0 THEN 1 ELSE 0 END) AS contacto_interesado,
                    
                    -- 4. Estudio de necesidades: Tienen la actividad en trazabilidad, pero NO oferta de servicios, y NO están en seguimiento/ganado/perdido
                    SUM(CASE WHEN COALESCE(tf.tiene_estudio_necesidades, 0) = 1 AND COALESCE(tf.tiene_oferta_servicios, 0) = 0 AND LOWER(TRIM(e.etapa_venta)) NOT IN ('seguimiento', 'ganado', 'perdido') THEN 1 ELSE 0 END) AS estudio_necesidades,
                    
                    -- 5. Oferta de Servicios: Tienen la actividad en trazabilidad, y NO están en seguimiento/ganado/perdido
                    SUM(CASE WHEN COALESCE(tf.tiene_oferta_servicios, 0) = 1 AND LOWER(TRIM(e.etapa_venta)) NOT IN ('seguimiento', 'ganado', 'perdido') THEN 1 ELSE 0 END) AS oferta_servicios,
                    
                    -- 6. Seguimiento a la oferta: Etapa explícita 'seguimiento'
                    SUM(CASE WHEN LOWER(TRIM(e.etapa_venta)) = 'seguimiento' THEN 1 ELSE 0 END) AS seguimiento_oferta,
                    
                    -- 7. Perdidos/No interesados
                    SUM(CASE WHEN LOWER(TRIM(e.etapa_venta)) = 'perdido' THEN 1 ELSE 0 END) AS perdidos,
                    
                    -- 8. Cierre exitoso
                    SUM(CASE WHEN LOWER(TRIM(e.etapa_venta)) = 'ganado' THEN 1 ELSE 0 END) AS cierre_exitoso,
                    
                    -- 9. Total Contactados (Todo el que pasó de prospectado)
                    SUM(CASE WHEN LOWER(TRIM(e.etapa_venta)) != 'prospectado' THEN 1 ELSE 0 END) AS total_contactados,
                    
                    -- 10. Total Empresas (Globales asignadas)
                    COUNT(e.id) AS total_empresas
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
                LEFT JOIN (
                    SELECT empresa_id FROM contactos GROUP BY empresa_id
                ) tc ON tc.empresa_id = e.id
                WHERE {$whereUserSql}
                GROUP BY u.id, u.nombre, u.email, u.rol
                ORDER BY u.nombre ASC";

        $params = array_merge($paramsEmp, $paramsUser);
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

    /**
     * Extrae todo el historial de trazabilidad de un usuario para el Excel.
     */
    public function detalleActividadesPorUsuario($usuarioId, $filtros = [])
    {
        $where = ["t.usuario_id = ?"];
        $params = [$usuarioId];

        if (!empty($filtros['fecha_inicio'])) {
            $where[] = "t.fecha >= ?";
            $params[] = $filtros['fecha_inicio'] . ' 00:00:00';
        }
        if (!empty($filtros['fecha_fin'])) {
            $where[] = "t.fecha <= ?";
            $params[] = $filtros['fecha_fin'] . ' 23:59:59';
        }

        $sql = "SELECT 
                    e.razon_social AS empresa,
                    e.etapa_venta AS etapa_actual,
                    t.tipo_actividad,
                    t.etapa_venta AS etapa_en_momento_actividad,
                    t.observaciones,
                    t.fecha AS fecha_actividad
                FROM trazabilidad t
                JOIN empresas e ON t.empresa_id = e.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY t.fecha ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
