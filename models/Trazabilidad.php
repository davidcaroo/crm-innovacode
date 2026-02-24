<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/BaseModel.php';

class Trazabilidad extends BaseModel
{
    protected $table      = 'trazabilidad';
    protected $primaryKey = 'id';

    public function registrar($data)
    {
        $sql  = "INSERT INTO trazabilidad (empresa_id, usuario_id, etapa_venta, tipo_actividad, observaciones) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['empresa_id'],
            $data['usuario_id'],
            $data['etapa_venta'],
            $data['tipo_actividad'] ?? 'nota',
            $data['observaciones'],
        ]);
    }

    public function historialPorEmpresa($empresa_id)
    {
        $sql  = "SELECT t.*, u.nombre AS usuario
                 FROM trazabilidad t
                 JOIN usuarios u ON t.usuario_id = u.id
                 WHERE t.empresa_id = ?
                 ORDER BY t.fecha DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$empresa_id]);
        return $stmt->fetchAll();
    }

    public function historialGlobal($usuario_id = null)
    {
        $sql = "SELECT t.*, u.nombre AS usuario, e.razon_social AS empresa
                FROM trazabilidad t
                JOIN usuarios u ON t.usuario_id = u.id
                JOIN empresas e ON t.empresa_id = e.id";

        $params = [];
        if ($usuario_id) {
            $sql .= " WHERE t.usuario_id = ?";
            $params[] = $usuario_id;
        }

        $sql .= " ORDER BY t.fecha DESC LIMIT 100";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
