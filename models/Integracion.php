<?php
// models/Integracion.php
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/Configuracion.php';

class Integracion extends BaseModel
{
    protected $table = 'integraciones';

    // --------------------------------------------------------
    // Lectura
    // --------------------------------------------------------
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM integraciones ORDER BY nombre ASC");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getBySlug(string $slug): ?object
    {
        $stmt = $this->db->prepare("SELECT * FROM integraciones WHERE slug = ?");
        $stmt->execute([$slug]);
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        if ($row && !empty($row->campos)) {
            $decoded = json_decode(Configuracion::desencriptar($row->campos), true);
            $row->campos_decoded = $decoded ?? [];
        } else {
            $row->campos_decoded = [];
        }
        return $row ?: null;
    }

    // --------------------------------------------------------
    // Escritura
    // --------------------------------------------------------
    public function guardar(string $slug, array $campos, string $estado = 'activa'): bool
    {
        $camposJson = Configuracion::encriptar(json_encode($campos));
        $stmt = $this->db->prepare(
            "UPDATE integraciones SET campos = ?, estado = ?, error_msg = NULL, actualizado_en = NOW() WHERE slug = ?"
        );
        return $stmt->execute([$camposJson, $estado, $slug]);
    }

    public function activar(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE integraciones SET estado = 'activa', error_msg = NULL WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function desactivar(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE integraciones SET estado = 'inactiva' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function registrarError(int $id, string $mensaje): bool
    {
        $stmt = $this->db->prepare("UPDATE integraciones SET estado = 'error', error_msg = ? WHERE id = ?");
        return $stmt->execute([$mensaje, $id]);
    }

    public function toggle(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT estado FROM integraciones WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;
        $nuevoEstado = $row['estado'] === 'activa' ? 'inactiva' : 'activa';
        $stmt2 = $this->db->prepare("UPDATE integraciones SET estado = ? WHERE id = ?");
        return $stmt2->execute([$nuevoEstado, $id]);
    }
}
