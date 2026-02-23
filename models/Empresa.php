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

    public function obtener($id)
    {
        $sql = "SELECT * FROM empresas WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function actualizar($id, $data)
    {
        $sql = "UPDATE empresas SET razon_social=?, dpto=?, ciudad=?, actividad_economica=?, correo_comercial=?, aplica=?, etapa_venta=?, observaciones=? WHERE id=?";
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
            $id
        ]);
    }

    public function eliminar($id)
    {
        $sql = "DELETE FROM empresas WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
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
    public function count()
    {
        $sql = "SELECT COUNT(*) as total FROM empresas";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result->total ?? 0;
    }

    public function contarPorDepartamento()
    {
        $sql = "SELECT dpto as departamento, COUNT(*) as conteo FROM empresas WHERE dpto IS NOT NULL AND dpto != '' GROUP BY dpto ORDER BY conteo DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function contarPorActividadEconomica()
    {
        $sql = "SELECT actividad_economica, COUNT(*) as conteo FROM empresas WHERE actividad_economica IS NOT NULL AND actividad_economica != '' GROUP BY actividad_economica ORDER BY conteo DESC LIMIT 10";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function contarPorEtapa()
    {
        $sql = "SELECT etapa_venta, COUNT(*) as conteo FROM empresas GROUP BY etapa_venta";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function empresasGanadasPorMes($anio = null)
    {
        $sql = "SELECT MONTH(creado_en) as mes, COUNT(*) as total FROM empresas WHERE etapa_venta = 'ganado' GROUP BY MONTH(creado_en) ORDER BY mes";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function contarUltimosDias($dias = 30)
    {
        $sql = "SELECT COUNT(*) as total FROM empresas WHERE creado_en >= DATE_SUB(NOW(), INTERVAL ? DAY)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$dias]);
        $result = $stmt->fetch();
        return $result->total ?? 0;
    }

    public function contarAnioActual()
    {
        $sql = "SELECT COUNT(*) as total FROM empresas WHERE YEAR(creado_en) = YEAR(NOW())";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result->total ?? 0;
    }
}
