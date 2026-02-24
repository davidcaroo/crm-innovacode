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
}
