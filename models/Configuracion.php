<?php
// models/Configuracion.php (fragmento para SMTP)
require_once __DIR__ . '/BaseModel.php';

class Configuracion extends BaseModel
{
    public static function getSMTP()
    {
        $db = self::getDB();
        $stmt = $db->prepare("SELECT smtp_host, smtp_port, smtp_user, smtp_pass, notificaciones_ganado FROM configuracion LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // Desencriptar smtp_pass
        if ($row) {
            $row['smtp_pass'] = self::desencriptar($row['smtp_pass']);
            $row['notificaciones_ganado'] = (bool)($row['notificaciones_ganado'] ?? false);
        }
        return $row;
    }
    public static function setSMTP($host, $port, $user, $pass, $notificaciones_ganado = false)
    {
        $db = self::getDB();
        $pass_enc = self::encriptar($pass);
        $notif = $notificaciones_ganado ? 1 : 0;
        $stmt = $db->prepare("UPDATE configuracion SET smtp_host=?, smtp_port=?, smtp_user=?, smtp_pass=?, notificaciones_ganado=?");
        $stmt->execute([$host, $port, $user, $pass_enc, $notif]);
    }
    private static function encriptar($texto)
    {
        // Implementa tu método seguro
        return base64_encode($texto);
    }
    private static function desencriptar($texto)
    {
        return base64_decode($texto);
    }
}
