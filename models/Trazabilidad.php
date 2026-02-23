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
}
