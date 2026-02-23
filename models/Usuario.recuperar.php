<?php
// models/Usuario.php (fragmentos para recuperación)
class Usuario extends BaseModel
{
    // ...existing code...
    public static function findByEmail($email)
    {
        $db = self::getDB();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function setRecoveryToken($usuarioId, $token)
    {
        $db = self::getDB();
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $stmt = $db->prepare("UPDATE usuarios SET recovery_token = ?, recovery_expira = ? WHERE id = ?");
        $stmt->execute([$token, $expira, $usuarioId]);
    }
    // ...existing code...
}
