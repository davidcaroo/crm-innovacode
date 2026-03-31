<?php
// models/PlantillaAdjunto.php
require_once __DIR__ . '/BaseModel.php';

class PlantillaAdjunto extends BaseModel
{
    protected $table = 'plantilla_adjuntos';

    public function obtenerPorPlantilla($plantilla_id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE plantilla_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$plantilla_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrar($plantilla_id, $nombre, $ruta)
    {
        $sql = "INSERT INTO {$this->table} (plantilla_id, nombre_original, ruta_archivo) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$plantilla_id, $nombre, $ruta]);
    }
}
