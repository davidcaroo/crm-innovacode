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
            $ok  = self::_enviarSmtp($smtp, $smtp['smtp_user'], $asunto, $cuerpo, $log);
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

    private static function _enviarSmtp(array $smtp, string $para, string $asunto, string $cuerpo, string &$log = ''): bool
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
        $read = function() use ($sock, &$log): string {
            $resp = '';
            while ($line = fgets($sock, 512)) {
                $resp .= $line;
                $log  .= '<- ' . rtrim($line) . "\n";
                if (strlen($line) >= 4 && $line[3] === ' ') break;
            }
            return $resp;
        };
        $write = function(string $cmd, bool $hide = false) use ($sock, &$log): void {
            $log .= '-> ' . ($hide ? '[oculto]' : rtrim($cmd)) . "\n";
            fwrite($sock, $cmd);
        };
        $code = fn(string $r): int => intval(substr(trim($r), 0, 3));

        // Banner
        $resp = $read();
        if ($code($resp) !== 220) { fclose($sock); $log .= "Error: esperaba banner 220\n"; return false; }

        // EHLO
        $write("EHLO crm-app\r\n");
        $resp = $read();
        if ($code($resp) !== 250) { fclose($sock); $log .= "EHLO fallido\n"; return false; }

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
        if ($code($resp) !== 334) { fclose($sock); $log .= "AUTH LOGIN no soportado\n"; return false; }

        $write(base64_encode($user) . "\r\n", true);
        $resp = $read();
        if ($code($resp) !== 334) { fclose($sock); $log .= "Error enviando usuario\n"; return false; }

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
        if ($code($resp) !== 250) { fclose($sock); $log .= "MAIL FROM fallido\n"; return false; }

        // RCPT TO
        $write("RCPT TO:<{$para}>\r\n");
        $resp = $read();
        if ($code($resp) !== 250) { fclose($sock); $log .= "RCPT TO fallido: " . trim($resp) . "\n"; return false; }

        // DATA
        $write("DATA\r\n");
        $resp = $read();
        if ($code($resp) !== 354) { fclose($sock); $log .= "DATA fallido\n"; return false; }

        $appName = defined('APP_NAME') ? APP_NAME : 'CRM';
        $msgId   = md5(uniqid()) . '@crm-app';
        $msg  = "Date: " . date('r') . "\r\n"
              . "From: {$appName} <{$user}>\r\n"
              . "To: <{$para}>\r\n"
              . "Message-ID: <{$msgId}>\r\n"
              . "Subject: =?UTF-8?B?" . base64_encode($asunto) . "?=\r\n"
              . "MIME-Version: 1.0\r\n"
              . "Content-Type: text/html; charset=UTF-8\r\n"
              . "Content-Transfer-Encoding: base64\r\n"
              . "\r\n"
              . chunk_split(base64_encode($cuerpo))
              . "\r\n.\r\n";

        fwrite($sock, $msg);
        $resp = $read();
        if ($code($resp) !== 250) { fclose($sock); $log .= "Mensaje rechazado: " . trim($resp) . "\n"; return false; }

        $write("QUIT\r\n");
        $read();
        fclose($sock);
        $log .= "Correo enviado correctamente\n";
        return true;
    }
}