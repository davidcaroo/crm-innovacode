<?php
// models/Mailer.php
require_once __DIR__ . '/Configuracion.php';

/**
 * Mailer  cliente SMTP puro en PHP (sin dependencias externas).
 * Soporta:
 *   - Puerto 465 con SSL implícito (ssl://)
 *   - Puerto 587/2525 con STARTTLS
 *   - AUTH LOGIN con base64
 */
class Mailer
{
    // ------------------------------------------------------------------
    // API pública
    // ------------------------------------------------------------------

    /**
     * Envía un correo usando la configuración SMTP guardada.
     * Solo envía si las notificaciones están activadas en la config.
     */
    public static function enviar(string $para, string $asunto, string $cuerpo): bool
    {
        $smtp = Configuracion::getSMTP();
        if (!$smtp || empty($smtp['smtp_host']) || empty($smtp['smtp_user'])) {
            return false;
        }
        if (!($smtp['notificaciones_ganado'] ?? false)) {
            return false;
        }
        return self::_enviarSmtp($smtp, $para, $asunto, $cuerpo);
    }

    /**
     * Envía email transaccional crítico (recuperación de contraseña, etc.)
     * NO exige que las notificaciones estén activas.
     */
    public static function enviarRecuperacion(string $para, string $asunto, string $cuerpo): bool
    {
        $smtp = Configuracion::getSMTP();
        if (!$smtp || empty($smtp['smtp_host']) || empty($smtp['smtp_user'])) {
            return false;
        }
        return self::_enviarSmtp($smtp, $para, $asunto, $cuerpo);
    }

    /**
     * Envía email de marketing o propuesta comercial.
     * NO exige que las notificaciones estén activas pues es una acción manual.
     */
    public static function enviarMarketing(string $para, string $asunto, string $cuerpo, array $adjuntos = []): bool
    {
        $smtp = Configuracion::getSMTP();
        if (!$smtp || empty($smtp['smtp_host']) || empty($smtp['smtp_user'])) {
            return false;
        }
        return self::_enviarSmtp($smtp, $para, $asunto, $cuerpo, $adjuntos);
    }

    /**
     * Envía credenciales a un nuevo usuario
     * Email transaccional que NO requiere notificaciones activas
     */
    public static function enviarCredencialesNuevoUsuario(string $nombre, string $email, string $passwordTemporal): bool
    {
        $smtp = Configuracion::getSMTP();
        if (!$smtp || empty($smtp['smtp_host']) || empty($smtp['smtp_user'])) {
            return false;
        }

        $appName = defined('APP_NAME') ? APP_NAME : 'CRM';
        $loginUrl = defined('BASE_URL') ? BASE_URL . '/usuario/login' : '#';

        $asunto = '¡Bienvenido(a) a ' . $appName . '! - Tus credenciales de acceso';

        $cuerpo = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:12px;box-shadow:0 4px 6px rgba(0,0,0,0.1);overflow:hidden;">
                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);padding:30px;text-align:center;">
                            <h1 style="margin:0;color:#ffffff;font-size:28px;font-weight:600;">¡Bienvenido(a)!</h1>
                            <p style="margin:8px 0 0 0;color:#dbeafe;font-size:16px;">Tu cuenta ha sido creada exitosamente</p>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding:40px 40px 30px 40px;">
                            <p style="margin:0 0 20px 0;color:#1f2937;font-size:16px;line-height:1.6;">
                                Hola <strong>' . htmlspecialchars($nombre) . '</strong>,
                            </p>
                            <p style="margin:0 0 24px 0;color:#4b5563;font-size:15px;line-height:1.6;">
                                Se ha creado una cuenta para ti en <strong>' . htmlspecialchars($appName) . '</strong>. 
                                A continuación encontrarás tus credenciales de acceso:
                            </p>
                            
                            <!-- Credentials Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f9ff;border-left:4px solid #3b82f6;border-radius:8px;margin:0 0 30px 0;">
                                <tr>
                                    <td style="padding:20px;">
                                        <p style="margin:0 0 12px 0;color:#1f2937;font-size:14px;">
                                            <strong>Usuario:</strong><br>
                                            <span style="color:#3b82f6;font-size:16px;font-weight:600;">' . htmlspecialchars($email) . '</span>
                                        </p>
                                        <p style="margin:0;color:#1f2937;font-size:14px;">
                                            <strong>Contraseña temporal:</strong><br>
                                            <code style="background:#ffffff;border:1px solid #cbd5e1;padding:8px 12px;border-radius:6px;font-size:18px;color:#1e40af;font-weight:600;display:inline-block;margin-top:4px;letter-spacing:1px;">' . htmlspecialchars($passwordTemporal) . '</code>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 24px 0;">
                                <tr>
                                    <td align="center" style="padding:20px 0;">
                                        <a href="' . htmlspecialchars($loginUrl) . '" 
                                           style="background-color:#28a745;color:#ffffff;text-decoration:none;padding:14px 32px;border-radius:8px;font-size:16px;font-weight:600;display:inline-block;box-shadow:0 2px 4px rgba(40,167,69,0.3);">
                                            Iniciar Sesión Ahora
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Warning Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fff5f5;border-left:4px solid #dc2626;border-radius:8px;margin:0 0 20px 0;">
                                <tr>
                                    <td style="padding:16px;">
                                        <p style="margin:0;color:#991b1b;font-size:14px;line-height:1.6;">
                                            <strong>⚠️ IMPORTANTE:</strong> Por seguridad, deberás cambiar esta contraseña temporal 
                                            <strong>al iniciar sesión por primera vez</strong>. No podrás acceder al sistema hasta 
                                            que completes este paso.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="margin:0;color:#6b7280;font-size:14px;line-height:1.6;">
                                Si tienes alguna duda, no dudes en contactar con el administrador del sistema.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f9fafb;padding:24px 40px;text-align:center;border-top:1px solid #e5e7eb;">
                            <p style="margin:0 0 8px 0;color:#9ca3af;font-size:13px;">
                                ' . htmlspecialchars($appName) . ' &copy; ' . date('Y') . '
                            </p>
                            <p style="margin:0;color:#9ca3af;font-size:12px;">
                                Este es un mensaje automático, por favor no respondas a este correo.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';

        return self::_enviarSmtp($smtp, $email, $asunto, $cuerpo);
    }

    /**
     * Prueba la conexión SMTP y envía un correo de prueba al mismo remitente.
     * Retorna ['ok' => bool, 'msg' => string, 'log' => string]
     */
    public static function probarConexion(): array
    {
        $smtp = Configuracion::getSMTP();
        if (empty($smtp['smtp_host']) || empty($smtp['smtp_user'])) {
            return ['ok' => false, 'msg' => 'SMTP no configurado (faltan host o usuario).', 'log' => ''];
        }
        if (empty($smtp['smtp_pass'])) {
            return ['ok' => false, 'msg' => 'La contraseña SMTP no está guardada. Guarda la configuración primero.', 'log' => ''];
        }

        $asunto = '[CRM] Prueba de conexión SMTP';
        $appName = defined('APP_NAME') ? APP_NAME : 'CRM';
        $cuerpo  = '<div style="font-family:sans-serif;max-width:480px;padding:24px;background:#ecfdf5;border-radius:12px;border:1px solid #6ee7b7;">'
            . '<h3 style="color:#065f46;margin-top:0;"> Conexión SMTP exitosa</h3>'
            . '<p>Esta es una prueba enviada desde <strong>' . htmlspecialchars($appName) . '</strong>.<br>Tu configuración de correo funciona correctamente.</p>'
            . '<p style="color:#6b7280;font-size:0.85em;">' . date('Y-m-d H:i:s') . '</p>'
            . '</div>';

        try {
            $log = '';
            $ok  = self::_enviarSmtp($smtp, $smtp['smtp_user'], $asunto, $cuerpo, [], $log);
            return [
                'ok'  => $ok,
                'msg' => $ok
                    ? 'Correo de prueba enviado a <strong>' . htmlspecialchars($smtp['smtp_user']) . '</strong>. Revisa tu bandeja.'
                    : 'No se pudo enviar. Revisa el log de conexión.',
                'log' => nl2br(htmlspecialchars($log)),
            ];
        } catch (Exception $e) {
            return ['ok' => false, 'msg' => 'Excepción: ' . $e->getMessage(), 'log' => ''];
        }
    }

    // ------------------------------------------------------------------
    // Motor SMTP privado
    // ------------------------------------------------------------------

    private static function _enviarSmtp(array $smtp, string $para, string $asunto, string $cuerpo, array $adjuntos = [], string &$log = ''): bool
    {
        $host = $smtp['smtp_host'];
        $port = intval($smtp['smtp_port'] ?? 465);
        $user = $smtp['smtp_user'];
        $pass = $smtp['smtp_pass'];

        $useSSL      = ($port === 465);
        $useStartTLS = ($port === 587 || $port === 2525);
        $socketHost  = $useSSL ? "ssl://{$host}" : $host;
        $timeout     = 20;

        $log .= "Conectando a {$socketHost}:{$port}...\n";

        $ctx  = stream_context_create(['ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
        ]]);
        $sock = @stream_socket_client("{$socketHost}:{$port}", $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $ctx);

        if (!$sock) {
            $log .= "FALLO conexion: [{$errno}] {$errstr}\n";
            return false;
        }
        stream_set_timeout($sock, $timeout);

        // helpers
        $read = function () use ($sock, &$log): string {
            $resp = '';
            while ($line = fgets($sock, 512)) {
                $resp .= $line;
                $log  .= '<- ' . rtrim($line) . "\n";
                if (strlen($line) >= 4 && $line[3] === ' ') break;
            }
            return $resp;
        };
        $write = function (string $cmd, bool $hide = false) use ($sock, &$log): void {
            $log .= '-> ' . ($hide ? '[oculto]' : rtrim($cmd)) . "\n";
            fwrite($sock, $cmd);
        };
        $code = fn(string $r): int => intval(substr(trim($r), 0, 3));

        // Banner
        $resp = $read();
        if ($code($resp) !== 220) {
            fclose($sock);
            $log .= "Error: esperaba banner 220\n";
            return false;
        }

        // EHLO
        $write("EHLO crm-app\r\n");
        $resp = $read();
        if ($code($resp) !== 250) {
            fclose($sock);
            $log .= "EHLO fallido\n";
            return false;
        }

        // STARTTLS
        if ($useStartTLS) {
            $write("STARTTLS\r\n");
            $resp = $read();
            if ($code($resp) === 220) {
                stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                $write("EHLO crm-app\r\n");
                $read();
            }
        }

        // AUTH LOGIN
        $write("AUTH LOGIN\r\n");
        $resp = $read();
        if ($code($resp) !== 334) {
            fclose($sock);
            $log .= "AUTH LOGIN no soportado\n";
            return false;
        }

        $write(base64_encode($user) . "\r\n", true);
        $resp = $read();
        if ($code($resp) !== 334) {
            fclose($sock);
            $log .= "Error enviando usuario\n";
            return false;
        }

        $write(base64_encode($pass) . "\r\n", true);
        $resp = $read();
        if ($code($resp) !== 235) {
            fclose($sock);
            $log .= "Autenticacion fallida: " . trim($resp) . "\n";
            return false;
        }
        $log .= "Autenticacion exitosa\n";

        // MAIL FROM
        $write("MAIL FROM:<{$user}>\r\n");
        $resp = $read();
        if ($code($resp) !== 250) {
            fclose($sock);
            $log .= "MAIL FROM fallido\n";
            return false;
        }

        // RCPT TO
        $write("RCPT TO:<{$para}>\r\n");
        $resp = $read();
        if ($code($resp) !== 250) {
            fclose($sock);
            $log .= "RCPT TO fallido: " . trim($resp) . "\n";
            return false;
        }

        // DATA
        $write("DATA\r\n");
        $resp = $read();
        if ($code($resp) !== 354) {
            fclose($sock);
            $log .= "DATA fallido\n";
            return false;
        }

        $appName  = defined('APP_NAME') ? APP_NAME : 'CRM';
        $boundary = "----=_NextPart_" . md5(uniqid());
        $msgId    = md5(uniqid()) . '@crm-app';

        $header = "Date: " . date('r') . "\r\n"
            . "From: {$appName} <{$user}>\r\n"
            . "To: <{$para}>\r\n"
            . "Message-ID: <{$msgId}>\r\n"
            . "Subject: =?UTF-8?B?" . base64_encode($asunto) . "?=\r\n"
            . "MIME-Version: 1.0\r\n";

        if (empty($adjuntos)) {
            $header .= "Content-Type: text/html; charset=UTF-8\r\n"
                . "Content-Transfer-Encoding: base64\r\n"
                . "\r\n"
                . chunk_split(base64_encode($cuerpo));
        } else {
            $header .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n"
                . "\r\n"
                . "--{$boundary}\r\n"
                . "Content-Type: text/html; charset=UTF-8\r\n"
                . "Content-Transfer-Encoding: base64\r\n"
                . "\r\n"
                . chunk_split(base64_encode($cuerpo));

            foreach ($adjuntos as $adj) {
                $ruta   = $adj['ruta'];
                $nombre = $adj['nombre'];
                if (file_exists($ruta)) {
                    $content = file_get_contents($ruta);
                    $header .= "\r\n--{$boundary}\r\n"
                        . "Content-Type: application/octet-stream; name=\"{$nombre}\"\r\n"
                        . "Content-Transfer-Encoding: base64\r\n"
                        . "Content-Disposition: attachment; filename=\"{$nombre}\"\r\n"
                        . "\r\n"
                        . chunk_split(base64_encode($content));
                }
            }
            $header .= "\r\n--{$boundary}--\r\n";
        }
        
        $msg = $header . "\r\n.\r\n";

        fwrite($sock, $msg);
        $resp = $read();
        if ($code($resp) !== 250) {
            fclose($sock);
            $log .= "Mensaje rechazado: " . trim($resp) . "\n";
            return false;
        }

        $write("QUIT\r\n");
        $read();
        fclose($sock);
        $log .= "Correo enviado correctamente\n";
        return true;
    }
}
