<?php
// models/Mailer.php
require_once __DIR__ . '/Configuracion.php';

class Mailer
{
    public static function enviar($para, $asunto, $cuerpo)
    {
        $smtp = Configuracion::getSMTP();
        if (!$smtp || !($smtp['notificaciones_ganado'] ?? false)) {
            return false; // Notificaciones desactivadas o sin configurar
        }

        // Si tuviéramos PHPMailer aquí, lo usaríamos.
        // Como no hay, usamos mail() con headers básicos.
        // ADVERTENCIA: mail() suele fallar en XAMPP si no está configurado sendmail.exe.

        $de = $smtp['smtp_user'] ?: 'crm@crm-bahari.com';
        $headers = "From: $de\r\n";
        $headers .= "Reply-To: $de\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        // En un entorno profesional, aquí se usaría fsockopen para SMTP real 
        // o PHPMailer. Por ahora, dejamos la estructura lista.
        return @mail($para, $asunto, $cuerpo, $headers);
    }
}
