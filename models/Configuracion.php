<?php
// models/Configuracion.php
require_once __DIR__ . '/BaseModel.php';

class Configuracion extends BaseModel
{
    // --------------------------------------------------------
    // SMTP
    // --------------------------------------------------------
    public static function getSMTP(): array
    {
        $db   = self::getDB();
        $stmt = $db->prepare("SELECT smtp_host, smtp_port, smtp_user, smtp_pass, notificaciones_ganado FROM configuracion LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $row['smtp_pass']            = self::desencriptar($row['smtp_pass'] ?? '');
            $row['notificaciones_ganado'] = (bool)($row['notificaciones_ganado'] ?? false);
        }

        return $row ?: [
            'smtp_host'            => '',
            'smtp_port'            => 587,
            'smtp_user'            => '',
            'smtp_pass'            => '',
            'notificaciones_ganado' => false,
        ];
    }

    public static function setSMTP(string $host, int $port, string $user, string $pass, bool $notificaciones_ganado = false): void
    {
        $db       = self::getDB();
        $pass_enc = self::encriptar($pass);
        $notif    = $notificaciones_ganado ? 1 : 0;

        // Upsert: si hay fila la actualiza, si no inserta
        $count = $db->query("SELECT COUNT(*) FROM configuracion")->fetchColumn();
        if ($count > 0) {
            $stmt = $db->prepare("UPDATE configuracion SET smtp_host=?, smtp_port=?, smtp_user=?, smtp_pass=?, notificaciones_ganado=?");
        } else {
            $stmt = $db->prepare("INSERT INTO configuracion (smtp_host, smtp_port, smtp_user, smtp_pass, notificaciones_ganado) VALUES (?,?,?,?,?)");
        }
        $stmt->execute([$host, $port, $user, $pass_enc, $notif]);
    }

    // --------------------------------------------------------
    // Estado SMTP (para mostrar badge en la vista)
    // --------------------------------------------------------
    public static function smtpConfigurado(): bool
    {
        $smtp = self::getSMTP();
        return !empty($smtp['smtp_host']) && !empty($smtp['smtp_user']);
    }

    // --------------------------------------------------------
    // Cifrado AES-256-CBC
    // --------------------------------------------------------
    public static function encriptar(string $texto): string
    {
        if (empty($texto)) return '';
        $key    = substr(hash('sha256', ENCRYPTION_KEY, true), 0, 32);
        $iv     = openssl_random_pseudo_bytes(16);
        $cipher = openssl_encrypt($texto, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($iv . $cipher);
    }

    public static function desencriptar(string $dato): string
    {
        if (empty($dato)) return '';
        try {
            $raw    = base64_decode($dato);
            // Compatibilidad: si no tiene IV (dato era base64 simple antiguo) devolver decode directo
            if (strlen($raw) <= 16) return base64_decode($dato);
            $key    = substr(hash('sha256', ENCRYPTION_KEY, true), 0, 32);
            $iv     = substr($raw, 0, 16);
            $cipher = substr($raw, 16);
            $result = openssl_decrypt($cipher, 'AES-256-CBC', $key, 0, $iv);
            return $result !== false ? $result : base64_decode($dato);
        } catch (Exception $e) {
            return base64_decode($dato); // fallback a base64 antiguo
        }
    }
}
