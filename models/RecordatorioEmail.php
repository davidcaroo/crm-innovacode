<?php

require_once __DIR__ . '/BaseModel.php';

class RecordatorioEmail extends BaseModel
{
    protected $table = 'recordatorios_email';

    public function crear(array $data)
    {
        $sql = "INSERT INTO {$this->table}
                    (empresa_id, usuario_id, tipo_recordatorio, asunto, mensaje_html, fecha_programada, estado, datos_json)
                VALUES
                    (?, ?, ?, ?, ?, ?, 'pendiente', ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['empresa_id'],
            $data['usuario_id'],
            $data['tipo_recordatorio'],
            $data['asunto'],
            $data['mensaje_html'],
            $data['fecha_programada'],
            $data['datos_json'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function cancelarPendientesPorEmpresaYTipo(int $empresa_id, array $tipos): int
    {
        if (empty($tipos)) {
            return 0;
        }

        $placeholders = implode(',', array_fill(0, count($tipos), '?'));
        $sql = "UPDATE {$this->table}
                SET estado = 'cancelado', fecha_ejecucion = NOW()
                WHERE empresa_id = ?
                  AND estado = 'pendiente'
                  AND tipo_recordatorio IN ($placeholders)";

        $params = array_merge([$empresa_id], array_values($tipos));
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount();
    }

    public function obtenerListado(array $filtros = [], ?int $usuario_id = null): array
    {
        $sql = "SELECT r.*, e.razon_social, e.correo_comercial, u.nombre AS usuario_nombre, u.email AS usuario_email
                FROM {$this->table} r
                INNER JOIN empresas e ON e.id = r.empresa_id
                INNER JOIN usuarios u ON u.id = r.usuario_id
                WHERE 1=1";

        $params = [];

        if ($usuario_id) {
            $sql .= " AND r.usuario_id = ?";
            $params[] = $usuario_id;
        }

        if (!empty($filtros['estado'])) {
            $sql .= " AND r.estado = ?";
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['tipo_recordatorio'])) {
            $sql .= " AND r.tipo_recordatorio = ?";
            $params[] = $filtros['tipo_recordatorio'];
        }

        if (!empty($filtros['empresa_id'])) {
            $sql .= " AND r.empresa_id = ?";
            $params[] = (int) $filtros['empresa_id'];
        }

        $sql .= " ORDER BY r.fecha_programada DESC, r.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function obtenerPendientesVencidos(int $limit = 100): array
    {
        $sql = "SELECT r.*, e.razon_social, e.correo_comercial, u.nombre AS usuario_nombre, u.email AS usuario_email
                FROM {$this->table} r
                INNER JOIN empresas e ON e.id = r.empresa_id
                INNER JOIN usuarios u ON u.id = r.usuario_id
                WHERE r.estado = 'pendiente'
                  AND r.fecha_programada <= NOW()
                ORDER BY r.fecha_programada ASC
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function marcarEnviado(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET estado = 'enviado', fecha_ejecucion = NOW(), error_msg = NULL WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function marcarFallido(int $id, string $error): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET estado = 'fallido', fecha_ejecucion = NOW(), error_msg = ? WHERE id = ?");
        return $stmt->execute([$error, $id]);
    }
}