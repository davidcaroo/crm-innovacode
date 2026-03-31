<?php

require_once __DIR__ . '/BaseModel.php';

class PlantillaEmail extends BaseModel {
    protected $table = 'plantillas_email'; // Removed type hint for PHP 8 compatibility with legacy code

    /**
     * Obtiene todas las plantillas, ordenadas por nombre
     */
    public function obtenerTodas() {
        $stmt = $this->db->prepare("SELECT p.*, u.nombre as usuario_nombre 
                                    FROM {$this->table} p 
                                    LEFT JOIN usuarios u ON p.usuario_id = u.id 
                                    ORDER BY p.nombre ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($data) {
        $sql = "INSERT INTO {$this->table} (nombre, asunto, cuerpo_html, usuario_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['nombre'],
            $data['asunto'],
            $data['cuerpo_html'],
            $data['usuario_id']
        ]);
        return $this->db->lastInsertId();
    }
}
