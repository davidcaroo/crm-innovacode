<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/BaseModel.php';

class Empresa extends BaseModel
{
    protected $table = 'empresas';
    protected $primaryKey = 'id';

    public function crear($data)
    {
        $sql = "INSERT INTO empresas (razon_social, dpto, ciudad, actividad_economica, correo_comercial, aplica, etapa_venta, observaciones, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['razon_social'],
            $data['dpto'],
            $data['ciudad'],
            $data['actividad_economica'],
            $data['correo_comercial'],
            $data['aplica'],
            $data['etapa_venta'],
            $data['observaciones'],
            $data['usuario_id']
        ]);
    }

    public function todasPorUsuario($usuario_id)
    {
        $sql = "SELECT * FROM empresas WHERE usuario_id = ? ORDER BY creado_en DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll();
    }

    public function todasAdmin()
    {
        $sql = "SELECT * FROM empresas ORDER BY creado_en DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function porEtapaPaginadas($etapa, $usuario_id = null, $limit = 20, $offset = 0)
    {
        $sql = "SELECT * FROM empresas WHERE etapa_venta = :etapa";
        if ($usuario_id) {
            $sql .= " AND usuario_id = :usuario_id";
        }
        $sql .= " ORDER BY creado_en DESC LIMIT :offset, :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':etapa', $etapa, PDO::PARAM_STR);
        if ($usuario_id) {
            $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
        }
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Buscar empresas por término (nombre, departamento o ciudad)
     */
    public function buscar($termino, $usuario_id = null)
    {
        $termino = '%' . $termino . '%';

        $sql = "SELECT * FROM empresas 
                WHERE (razon_social LIKE ? OR dpto LIKE ? OR ciudad LIKE ?)";
        $params = [$termino, $termino, $termino];

        // Filtrar por usuario si no es admin
        if ($usuario_id) {
            $sql .= " AND usuario_id = ?";
            $params[] = $usuario_id;
        }

        $sql .= " ORDER BY creado_en DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function obtener($id, $usuario_id = null)
    {
        $sql = "SELECT * FROM empresas WHERE id = ?";
        $params = [$id];

        if ($usuario_id) {
            $sql .= " AND usuario_id = ?";
            $params[] = $usuario_id;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function obtenerFiltroDataTables($usuario_id, $start, $length, $searchValue, $orderColumn, $orderDir)
    {
        $params = [];
        $sql = "SELECT e.* FROM empresas e WHERE 1=1";
        
        if ($usuario_id) {
            $sql .= " AND e.usuario_id = :usuario_id";
            $params[':usuario_id'] = $usuario_id;
        }

        if (!empty($searchValue)) {
            $sql .= " AND (e.razon_social LIKE :search1 OR e.dpto LIKE :search2 OR e.ciudad LIKE :search3 OR e.correo_comercial LIKE :search4)";
            $searchParam = '%' . $searchValue . '%';
            $params[':search1'] = $searchParam;
            $params[':search2'] = $searchParam;
            $params[':search3'] = $searchParam;
            $params[':search4'] = $searchParam;
        }

        $columnsMapping = [
            0 => 'e.razon_social',
            1 => 'e.dpto',
            2 => 'e.ciudad',
            3 => 'e.actividad_economica',
            4 => 'e.correo_comercial',
            5 => 'e.etapa_venta'
        ];
        
        $orderColName = $columnsMapping[$orderColumn] ?? 'e.creado_en';
        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';

        $sql .= " ORDER BY {$orderColName} {$orderDir}";
        
        if ($length > 0) {
            $sql .= " LIMIT :start, :length";
        }

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        if ($length > 0) {
            $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
            $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function contarFiltroDataTables($usuario_id, $searchValue)
    {
        $params = [];
        $sql = "SELECT COUNT(*) as total FROM empresas e WHERE 1=1";
        
        if ($usuario_id) {
            $sql .= " AND e.usuario_id = :usuario_id";
            $params[':usuario_id'] = $usuario_id;
        }

        if (!empty($searchValue)) {
            $sql .= " AND (e.razon_social LIKE :search1 OR e.dpto LIKE :search2 OR e.ciudad LIKE :search3 OR e.correo_comercial LIKE :search4)";
            $searchParam = '%' . $searchValue . '%';
            $params[':search1'] = $searchParam;
            $params[':search2'] = $searchParam;
            $params[':search3'] = $searchParam;
            $params[':search4'] = $searchParam;
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row ? (int)$row->total : 0;
    }

    public function actualizar($id, $data, $usuario_id = null)
    {
        $sql = "UPDATE empresas SET razon_social=?, dpto=?, ciudad=?, actividad_economica=?, correo_comercial=?, aplica=?, etapa_venta=?, observaciones=? WHERE id=?";
        $params = [
            $data['razon_social'],
            $data['dpto'],
            $data['ciudad'],
            $data['actividad_economica'],
            $data['correo_comercial'],
            $data['aplica'],
            $data['etapa_venta'],
            $data['observaciones'],
            $id
        ];

        if ($usuario_id) {
            $sql .= " AND usuario_id = ?";
            $params[] = $usuario_id;
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function eliminar($id, $usuario_id = null)
    {
        try {
            // Iniciar transacción para asegurar integridad
            $this->db->beginTransaction();

            // 1. Eliminar registros relacionados en trazabilidad
            $sqlTrazabilidad = "DELETE FROM trazabilidad WHERE empresa_id = ?";
            $stmtTraz = $this->db->prepare($sqlTrazabilidad);
            $stmtTraz->execute([$id]);

            // 2. Eliminar registros relacionados en contactos
            $sqlContactos = "DELETE FROM contactos WHERE empresa_id = ?";
            $stmtCont = $this->db->prepare($sqlContactos);
            $stmtCont->execute([$id]);

            // 3. Eliminar la empresa
            $sql = "DELETE FROM empresas WHERE id = ?";
            $params = [$id];

            if ($usuario_id) {
                $sql .= " AND usuario_id = ?";
                $params[] = $usuario_id;
            }

            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($params);

            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Actualizar solo la etapa de venta de una empresa (usado desde trazabilidad)
     */
    public function actualizarEtapa($id, $etapa)
    {
        $sql  = "UPDATE empresas SET etapa_venta = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$etapa, $id]);
    }

    /**
     * Importación masiva de empresas desde un array de datos
     */
    public function importarMasivo($filas, $usuario_id)
    {
        try {
            $this->db->beginTransaction();
            $sql = "INSERT INTO empresas (razon_social, dpto, ciudad, actividad_economica, correo_comercial, aplica, etapa_venta, observaciones, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);

            foreach ($filas as $fila) {
                $razon_social = trim($fila['razon_social'] ?? '');
                if (empty($razon_social)) continue;

                // Normalización de etapas para coincidir con el ENUM
                $etapa_raw = strtolower(trim($fila['etapa_venta'] ?? 'prospectado'));
                $etapa = 'prospectado';

                if (strpos($etapa_raw, 'ganad') !== false || strpos($etapa_raw, 'cierre') !== false) $etapa = 'ganado';
                elseif (strpos($etapa_raw, 'perdid') !== false || strpos($etapa_raw, 'no ') !== false) $etapa = 'perdido';
                elseif (strpos($etapa_raw, 'seguim') !== false) $etapa = 'seguimiento';
                elseif (strpos($etapa_raw, 'negocia') !== false) $etapa = 'negociacion';
                elseif (strpos($etapa_raw, 'contac') !== false) $etapa = 'contactado';

                $stmt->execute([
                    $razon_social,
                    trim($fila['dpto'] ?? 'No definido'),
                    trim($fila['ciudad'] ?? 'No definido'),
                    trim($fila['actividad_economica'] ?? 'No definido'),
                    trim($fila['correo_comercial'] ?? ''),
                    trim($fila['aplica'] ?? 'Si'),
                    $etapa,
                    trim($fila['observaciones'] ?? ''),
                    $usuario_id
                ]);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    // Métodos para estadísticas del dashboard
    public function count($usuario_id = null)
    {
        $sql = "SELECT COUNT(*) as total FROM empresas";
        if ($usuario_id) {
            $sql .= " WHERE usuario_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$usuario_id]);
        } else {
            $stmt = $this->db->query($sql);
        }
        $result = $stmt->fetch();
        return $result->total ?? 0;
    }

    public function contarPorDepartamento($usuario_id = null)
    {
        $sql = "SELECT dpto as departamento, COUNT(*) as conteo FROM empresas WHERE dpto IS NOT NULL AND dpto != ''";
        if ($usuario_id) {
            $sql .= " AND usuario_id = ?";
        }
        $sql .= " GROUP BY dpto ORDER BY conteo DESC";

        if ($usuario_id) {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$usuario_id]);
            return $stmt->fetchAll();
        }
        return $this->db->query($sql)->fetchAll();
    }

    public function contarPorActividadEconomica($usuario_id = null)
    {
        $sql = "SELECT actividad_economica, COUNT(*) as conteo FROM empresas WHERE actividad_economica IS NOT NULL AND actividad_economica != ''";
        if ($usuario_id) {
            $sql .= " AND usuario_id = ?";
        }
        $sql .= " GROUP BY actividad_economica ORDER BY conteo DESC LIMIT 10";

        if ($usuario_id) {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$usuario_id]);
            return $stmt->fetchAll();
        }
        return $this->db->query($sql)->fetchAll();
    }

    public function contarPorEtapa($usuario_id = null)
    {
        $sql = "SELECT etapa_venta, COUNT(*) as conteo FROM empresas";
        if ($usuario_id) {
            $sql .= " WHERE usuario_id = ?";
        }
        $sql .= " GROUP BY etapa_venta";

        if ($usuario_id) {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$usuario_id]);
            return $stmt->fetchAll();
        }
        return $this->db->query($sql)->fetchAll();
    }

    public function empresasGanadasPorMes($usuario_id = null)
    {
        $sql = "SELECT MONTH(creado_en) as mes, COUNT(*) as total FROM empresas WHERE etapa_venta = 'ganado'";
        if ($usuario_id) {
            $sql .= " AND usuario_id = ?";
        }
        $sql .= " GROUP BY MONTH(creado_en) ORDER BY mes";

        if ($usuario_id) {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$usuario_id]);
            return $stmt->fetchAll();
        }
        return $this->db->query($sql)->fetchAll();
    }

    public function empresasPerdidasPorMes($usuario_id = null)
    {
        $sql = "SELECT MONTH(creado_en) as mes, COUNT(*) as total FROM empresas WHERE LOWER(TRIM(etapa_venta)) IN ('perdido', 'perdida')";
        if ($usuario_id) {
            $sql .= " AND usuario_id = ?";
        }
        $sql .= " GROUP BY MONTH(creado_en) ORDER BY mes";

        if ($usuario_id) {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$usuario_id]);
            return $stmt->fetchAll();
        }
        return $this->db->query($sql)->fetchAll();
    }

    public function contarUltimosDias($dias = 30, $usuario_id = null)
    {
        $sql = "SELECT COUNT(*) as total FROM empresas WHERE creado_en >= DATE_SUB(NOW(), INTERVAL ? DAY)";
        if ($usuario_id) {
            $sql .= " AND usuario_id = ?";
        }
        $stmt = $this->db->prepare($sql);
        if ($usuario_id) {
            $stmt->execute([$dias, $usuario_id]);
        } else {
            $stmt->execute([$dias]);
        }
        $result = $stmt->fetch();
        return $result->total ?? 0;
    }

    /**
     * Cuenta gestiones en los últimos N días.
     * Una gestión válida debe cumplir:
     * - Empresa con etapa actual contactado/contactada
     * - Empresa con aplica SI/SÍ
     * - Registro de trazabilidad en el periodo con etapa contactado/contactada
     */
    public function contarGestionesUltimosDias($dias = 30, $usuario_id = null)
    {
        $sql = "SELECT COUNT(DISTINCT e.id) as total
                                FROM empresas e
                                INNER JOIN trazabilidad t ON t.empresa_id = e.id
                                WHERE t.fecha >= DATE_SUB(NOW(), INTERVAL ? DAY)
                                    AND LOWER(TRIM(e.etapa_venta)) IN ('contactado', 'contactada')
                                    AND LOWER(TRIM(t.etapa_venta)) IN ('contactado', 'contactada')
                                    AND UPPER(TRIM(COALESCE(e.aplica, ''))) IN ('SI', 'SÍ')";

        if ($usuario_id) {
            $sql .= " AND e.usuario_id = ?";
        }

        $stmt = $this->db->prepare($sql);
        if ($usuario_id) {
            $stmt->execute([$dias, $usuario_id]);
        } else {
            $stmt->execute([$dias]);
        }

        $result = $stmt->fetch();
        return $result->total ?? 0;
    }

    public function contarAnioActual($usuario_id = null)
    {
        $sql = "SELECT COUNT(*) as total FROM empresas WHERE YEAR(creado_en) = YEAR(NOW())";
        if ($usuario_id) {
            $sql .= " AND usuario_id = ?";
        }
        if ($usuario_id) {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$usuario_id]);
        } else {
            $stmt = $this->db->query($sql);
        }
        $result = $stmt->fetch();
        return $result->total ?? 0;
    }

    /**
     * Cuenta gestiones del año en curso (suma equivalente al total mensual del año).
     */
    public function contarGestionesAnioActual($usuario_id = null)
    {
        $sql = "SELECT COUNT(*) as total
                FROM empresas
                WHERE YEAR(creado_en) = YEAR(NOW())
                  AND LOWER(TRIM(etapa_venta)) IN ('contactado', 'contactada')
                  AND UPPER(TRIM(COALESCE(aplica, ''))) IN ('SI', 'SÍ')";

        if ($usuario_id) {
            $sql .= " AND usuario_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$usuario_id]);
        } else {
            $stmt = $this->db->query($sql);
        }

        $result = $stmt->fetch();
        return $result->total ?? 0;
    }

    /**
     * Cuenta empresas/gestiones ganadas por estado de pipeline actual.
     */
    public function contarGestionesGanadas($usuario_id = null)
    {
        $sql = "SELECT COUNT(*) as total
                FROM empresas
                WHERE LOWER(TRIM(etapa_venta)) IN ('ganado', 'ganada')";

        if ($usuario_id) {
            $sql .= " AND usuario_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$usuario_id]);
        } else {
            $stmt = $this->db->query($sql);
        }

        $result = $stmt->fetch();
        return $result->total ?? 0;
    }

    /**
     * Obtiene las empresas filtradas para el módulo de Email Marketing.
     * Criterio: Deben haber pasado el "Estudio de Necesidades" y estar listas para la "Oferta de Servicios".
     */
    public function obtenerParaMarketing($usuario_id = null)
    {
        $sql = "SELECT e.*, 
                (SELECT COUNT(*) FROM trazabilidad t WHERE t.empresa_id = e.id AND LOWER(t.tipo_actividad) IN ('estudio_necesidades', 'estudio de necesidades')) as tiene_estudio,
                (SELECT COUNT(*) FROM trazabilidad t WHERE t.empresa_id = e.id AND LOWER(t.tipo_actividad) IN ('oferta_servicio', 'oferta de servicio', 'oferta de servicios')) as tiene_oferta
                FROM empresas e
                WHERE e.etapa_venta NOT IN ('ganado', 'perdido')
                AND (e.etapa_venta = 'negociacion' OR EXISTS (
                    SELECT 1 FROM trazabilidad t 
                    WHERE t.empresa_id = e.id 
                    AND LOWER(t.tipo_actividad) IN ('estudio_necesidades', 'estudio de necesidades')
                ))";

        if ($usuario_id) {
            $sql .= " AND e.usuario_id = :usuario_id";
        }

        $sql .= " ORDER BY e.razon_social ASC";

        $stmt = $this->db->prepare($sql);
        if ($usuario_id) {
            $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
        }
        $stmt->execute();
        // Forzamos FETCH_ASSOC para compatibilidad con la vista
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
