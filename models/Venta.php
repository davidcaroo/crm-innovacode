<?php

/**
 * Modelo Venta - Ventas asociadas a Empresas (no clientes legacy)
 */
require_once __DIR__ . '/BaseModel.php';

class Venta extends BaseModel
{
    protected $table = 'ventas';

    /**
     * Registrar una venta vinculada a una empresa
     */
    public function agregar($empresa_id, $monto, $fecha, $usuario_id)
    {
        $this->validateRequired($empresa_id, 'Empresa');
        $this->validateRequired($monto, 'Monto');
        $this->validateRequired($fecha, 'Fecha');
        $this->validateNumeric($monto, 'Monto');

        $sql = "INSERT INTO ventas (empresa_id, monto, fecha, usuario_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$empresa_id, $monto, $fecha, $usuario_id]);
    }

    /**
     * Obtener todas las ventas con datos de la empresa
     * Filtra por usuario si no es admin
     */
    public function obtenerConEmpresa($usuario_id = null)
    {
        $sql = "SELECT v.*, e.razon_social AS empresa_nombre, e.dpto AS departamento
                FROM ventas v
                INNER JOIN empresas e ON v.empresa_id = e.id ";

        if ($usuario_id) {
            $sql .= " WHERE v.usuario_id = ? ";
        }

        $sql .= " ORDER BY v.fecha DESC";

        if ($usuario_id) {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$usuario_id]);
            return $stmt->fetchAll();
        }

        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Obtener ventas de una empresa especifica
     */
    public function obtenerPorEmpresa($empresa_id)
    {
        $sql = "SELECT v.*, e.razon_social AS empresa_nombre
                FROM ventas v
                INNER JOIN empresas e ON v.empresa_id = e.id
                WHERE v.empresa_id = ?
                ORDER BY v.fecha DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$empresa_id]);
        return $stmt->fetchAll();
    }

    /**
     * Total de ventas por mes en el ano actual
     */
    public function totalPorMes($anio = null)
    {
        if (!$anio) $anio = date('Y');
        $sql = "SELECT MONTH(fecha) as mes, SUM(monto) as total
                FROM ventas
                WHERE YEAR(fecha) = ?
                GROUP BY MONTH(fecha)
                ORDER BY mes";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$anio]);
        return $stmt->fetchAll();
    }

    /**
     * Total acumulado de ventas
     */
    public function totalGeneral()
    {
        $stmt = $this->db->query("SELECT COALESCE(SUM(monto),0) AS total FROM ventas");
        return $stmt->fetch()->total ?? 0;
    }

    /**
     * Eliminar una venta (Validando propiedad para evitar IDOR)
     */
    public function delete($id, $usuario_id = null)
    {
        $sql = "DELETE FROM ventas WHERE id = ?";
        $params = [$id];

        if ($usuario_id) {
            $sql .= " AND usuario_id = ?";
            $params[] = $usuario_id;
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}
