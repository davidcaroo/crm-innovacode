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
}
