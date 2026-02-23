<?php
// models/Configuracion.php (fragmento para SMTP)
class Configuracion extends BaseModel
{
    public static function getSMTP()
    {
        $db = self::getDB();
        $stmt = $db->prepare("SELECT smtp_host, smtp_port, smtp_user, smtp_pass FROM configuracion LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // Desencriptar smtp_pass
        if ($row) {
            $row['smtp_pass'] = self::desencriptar($row['smtp_pass']);
        }
        return $row;
    }
    public static function setSMTP($host, $port, $user, $pass)
    {
        $db = self::getDB();
        $pass_enc = self::encriptar($pass);
        $stmt = $db->prepare("UPDATE configuracion SET smtp_host=?, smtp_port=?, smtp_user=?, smtp_pass=?");
        $stmt->execute([$host, $port, $user, $pass_enc]);
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
