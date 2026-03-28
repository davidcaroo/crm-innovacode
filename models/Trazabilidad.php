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

    /**
     * Retorna estados de flujo por empresa para pintar badges en listados.
     * @param array $empresaIds
     * @return array [empresa_id => ['tiene_estudio_necesidades' => bool, 'tiene_oferta_servicios' => bool]]
     */
    public function obtenerEstadosFlujoEmpresas($empresaIds = [])
    {
        $ids = array_values(array_filter(array_map('intval', (array)$empresaIds), function ($id) {
            return $id > 0;
        }));

        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT
                    empresa_id,
                    MAX(CASE WHEN LOWER(tipo_actividad) IN ('estudio_necesidades', 'estudio de necesidades') THEN 1 ELSE 0 END) AS tiene_estudio_necesidades,
                    MAX(CASE WHEN LOWER(tipo_actividad) IN ('oferta_servicio', 'oferta de servicio', 'oferta de servicios') THEN 1 ELSE 0 END) AS tiene_oferta_servicios
                FROM trazabilidad
                WHERE empresa_id IN ($placeholders)
                GROUP BY empresa_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($ids);
        $rows = $stmt->fetchAll();

        $estadoPorEmpresa = [];
        foreach ($ids as $id) {
            $estadoPorEmpresa[$id] = [
                'tiene_estudio_necesidades' => false,
                'tiene_oferta_servicios' => false,
            ];
        }

        foreach ($rows as $row) {
            $empresaId = (int)($row->empresa_id ?? 0);
            if ($empresaId > 0) {
                $estadoPorEmpresa[$empresaId] = [
                    'tiene_estudio_necesidades' => ((int)($row->tiene_estudio_necesidades ?? 0) === 1),
                    'tiene_oferta_servicios' => ((int)($row->tiene_oferta_servicios ?? 0) === 1),
                ];
            }
        }

        return $estadoPorEmpresa;
    }

    /**
     * Obtiene historial completo para exportación (sin límite de registros)
     * @param array $filtros ['usuario_id', 'empresa_id', 'fecha_inicio', 'fecha_fin', 'tipo_actividad']
     * @return array
     */
    public function historialParaExportar($filtros = [])
    {
        $sql = "SELECT 
                    t.id,
                    t.fecha,
                    DATE_FORMAT(t.fecha, '%d/%m/%Y %H:%i') as fecha_formateada,
                    e.id as empresa_id,
                    e.razon_social as empresa,
                    e.dpto,
                    e.ciudad,
                    e.actividad_economica,
                    e.correo_comercial,
                    e.etapa_venta as etapa_actual_empresa,
                    u.id as usuario_id,
                    u.nombre as usuario,
                    u.rol as rol_usuario,
                    t.tipo_actividad,
                    t.etapa_venta as etapa_en_actividad,
                    t.observaciones,
                    DATEDIFF(NOW(), t.fecha) as dias_transcurridos
                FROM trazabilidad t
                INNER JOIN usuarios u ON t.usuario_id = u.id
                INNER JOIN empresas e ON t.empresa_id = e.id
                WHERE 1=1";

        $params = [];

        // Filtro por usuario (permisos)
        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND t.usuario_id = ?";
            $params[] = $filtros['usuario_id'];
        }

        // Filtro por empresa específica
        if (!empty($filtros['empresa_id'])) {
            $sql .= " AND t.empresa_id = ?";
            $params[] = $filtros['empresa_id'];
        }

        // Filtro por fecha inicio
        if (!empty($filtros['fecha_inicio'])) {
            $sql .= " AND t.fecha >= ?";
            $params[] = $filtros['fecha_inicio'] . ' 00:00:00';
        }

        // Filtro por fecha fin
        if (!empty($filtros['fecha_fin'])) {
            $sql .= " AND t.fecha <= ?";
            $params[] = $filtros['fecha_fin'] . ' 23:59:59';
        }

        // Filtro por tipo de actividad
        if (!empty($filtros['tipo_actividad'])) {
            $sql .= " AND t.tipo_actividad = ?";
            $params[] = $filtros['tipo_actividad'];
        }

        $sql .= " ORDER BY t.fecha DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
