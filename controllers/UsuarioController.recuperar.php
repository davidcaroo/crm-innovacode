<?php
// controllers/UsuarioController.php (fragmento para recuperación)
class UsuarioController extends BaseController
{
    // ...existing code...
    public function recuperar()
    {
        $mensaje = "";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            if ($email) {
                // Buscar usuario por email
                $usuario = Usuario::findByEmail($email);
                if ($usuario) {
                    // Generar token seguro
                    $token = bin2hex(random_bytes(32));
                    // Guardar token y expiración en DB
                    Usuario::setRecoveryToken($usuario['id'], $token);
                    // Enviar correo con enlace
                    $this->enviarCorreoRecuperacion($email, $token);
                    $mensaje = "Se ha enviado un enlace de recuperación a tu correo.";
                } else {
                    $mensaje = "El correo no está registrado.";
                }
            }
        }
        $this->view('usuarios/recuperar', ['mensaje' => $mensaje], false);
    }

    private function enviarCorreoRecuperacion($email, $token)
    {
        // Obtener configuración SMTP desde DB (desencriptar)
        $smtp = Configuracion::getSMTP();
        // Enviar correo usando mail() o PHPMailer
        // ...implementación SMTP...
        // Ejemplo básico:
        $enlace = url("usuario/resetear", ['token' => $token]);
        $asunto = "Recuperación de contraseña CRM";
        $mensaje = "Haz clic en el siguiente enlace para restablecer tu contraseña: $enlace";
        // mail($email, $asunto, $mensaje); // Reemplazar por SMTP real
    }
    // ...existing code...
}
