<?php

require_once __DIR__ . '/BaseModel.php';

class EnvioEmail extends BaseModel {
    protected $table = 'envios_email';

    /**
     * Obtiene el historial de correos enviados a una empresa
     */
    public function obtenerPorEmpresa($empresa_id) {
        $stmt = $this->db->prepare("SELECT e.*, u.nombre as usuario_nombre 
                                    FROM {$this->table} e 
                                    LEFT JOIN usuarios u ON e.usuario_id = u.id 
                                    WHERE e.empresa_id = :empresa_id 
                                    ORDER BY e.creado_en DESC");
        $stmt->execute(['empresa_id' => $empresa_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Guarda el registro de un envío de correo
     */
    public function registrarEnvio($empresa_id, $asunto, $tipo_envio, $estado, $usuario_id, $error_msg = null) {
        $sql = "INSERT INTO {$this->table} (empresa_id, asunto, tipo_envio, estado, error_msg, usuario_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $empresa_id,
            $asunto,
            $tipo_envio,
            $estado,
            $error_msg,
            $usuario_id
        ]);
    }
}
