<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Mailer.php';
require_once __DIR__ . '/BaseController.php';

class UsuarioController extends BaseController
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $this->post('email');
            $password = $this->post('password');
            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->validarLogin($email, $password);
            if ($usuario) {
                if ($usuario->estado === 'inactivo') {
                    $this->view('usuarios/login', ['error' => 'Tu cuenta está inactiva. Contacta al administrador.']);
                    return;
                }
                $_SESSION['usuario_id'] = $usuario->id;
                $_SESSION['usuario_rol'] = $usuario->rol;
                $_SESSION['usuario_nombre'] = $usuario->nombre;
                $this->redirect(BASE_URL . '/index.php');
            } else {
                $this->view('usuarios/login', ['error' => 'Credenciales inválidas']);
            }
        } else {
            $this->view('usuarios/login');
        }
    }

    /**
     * Modo Espectador (Impersonate)
     * Permite a un Superadmin entrar como otro usuario
     */
    public function impersonate()
    {
        if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'superadmin') {
            $this->redirect(BASE_URL . '/index.php');
        }

        $idDestino = $this->get('id');
        if (!$idDestino) {
            $this->redirect(BASE_URL . '/index.php?controller=usuario&action=lista');
        }

        $usuarioModel = new Usuario();
        $usuarioDestino = $usuarioModel->obtener($idDestino);

        if ($usuarioDestino) {
            // Guardar datos actuales del admin para poder volver
            if (!isset($_SESSION['is_impersonating'])) {
                $_SESSION['admin_id_original'] = $_SESSION['usuario_id'];
                $_SESSION['admin_nombre_original'] = $_SESSION['usuario_nombre'];
                $_SESSION['admin_rol_original'] = $_SESSION['usuario_rol'];
                $_SESSION['is_impersonating'] = true;
            }

            // Cambiar identidad
            $_SESSION['usuario_id'] = $usuarioDestino->id;
            $_SESSION['usuario_nombre'] = $usuarioDestino->nombre;
            $_SESSION['usuario_rol'] = $usuarioDestino->rol;

            $this->redirect(BASE_URL . '/index.php');
        } else {
            $this->redirect(BASE_URL . '/index.php?controller=usuario&action=lista');
        }
    }

    /**
     * Volver a la cuenta de Superadmin
     */
    public function stopImpersonating()
    {
        if (isset($_SESSION['is_impersonating']) && $_SESSION['is_impersonating']) {
            $_SESSION['usuario_id'] = $_SESSION['admin_id_original'];
            $_SESSION['usuario_nombre'] = $_SESSION['admin_nombre_original'];
            $_SESSION['usuario_rol'] = $_SESSION['admin_rol_original'];

            unset($_SESSION['admin_id_original']);
            unset($_SESSION['admin_nombre_original']);
            unset($_SESSION['admin_rol_original']);
            unset($_SESSION['is_impersonating']);

            $this->redirect(BASE_URL . '/index.php?controller=usuario&action=lista');
        } else {
            $this->redirect(BASE_URL . '/index.php');
        }
    }

    public function logout()
    {
        session_destroy();
        $this->redirect(BASE_URL . '/index.php?controller=usuario&action=login');
    }

    /* ============================
       CRUD de Usuarios (Admin)
       ============================ */

    private function requireAdmin()
    {
        if (!isset($_SESSION['usuario_rol']) || !in_array($_SESSION['usuario_rol'], ['admin', 'superadmin'])) {
            $this->redirect(BASE_URL . '/index.php');
        }
    }

    public function lista()
    {
        $this->requireAdmin();
        $usuarioModel = new Usuario();
        $this->view('usuarios/lista', ['usuarios' => $usuarioModel->todos()]);
    }

    public function crearUsuario()
    {
        $this->requireAdmin();
        $this->view('usuarios/crear');
    }

    public function guardarUsuario()
    {
        $this->requireAdmin();
        if (!$this->isPost()) {
            $this->redirect(BASE_URL . '/index.php?controller=usuario&action=lista');
            return;
        }
        $usuarioModel = new Usuario();
        $usuarioModel->crear(
            $this->post('nombre'),
            $this->post('email'),
            $this->post('password'),
            $this->post('rol') ?: 'usuario'
        );
        $this->redirect(BASE_URL . '/index.php?controller=usuario&action=lista');
    }

    public function editarUsuario()
    {
        $this->requireAdmin();
        $id           = $this->get('id');
        $usuarioModel = new Usuario();
        $usuario      = $usuarioModel->obtener($id);
        $this->view('usuarios/editar', ['usuario' => $usuario]);
    }

    public function actualizarUsuario()
    {
        $this->requireAdmin();
        if (!$this->isPost()) {
            $this->redirect(BASE_URL . '/index.php?controller=usuario&action=lista');
            return;
        }
        $id           = $this->post('id');
        $usuarioModel = new Usuario();
        $rol          = $this->post('rol');
        $estado       = $this->post('estado') ?: 'activo';

        $usuarioModel->actualizar($id, $this->post('nombre'), $this->post('email'), $rol ?: 'usuario', $estado);

        $nueva = $this->post('password');
        if ($nueva) {
            $usuarioModel->cambiarPassword($id, $nueva);
        }
        $this->redirect(BASE_URL . '/index.php?controller=usuario&action=lista');
    }

    public function eliminarUsuario()
    {
        $this->requireAdmin();
        $id = $this->get('id');
        // Proteger al superadmin y al usuario actual
        if ($id && $id != $_SESSION['usuario_id']) {
            $usuarioModel = new Usuario();
            $usuarioModel->eliminar($id);
        }
        $this->redirect(BASE_URL . '/index.php?controller=usuario&action=lista');
    }

    /* ============================
       Recuperación de contraseña
       ============================ */

    /**
     * Paso 1: el usuario ingresa su email.
     * - GET  → mostrar formulario
     * - POST → generar token, guardar hasheado, enviar email
     */
    public function recuperar()
    {
        if ($this->isPost()) {
            $email = trim($this->post('email'));
            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->buscarPorEmail($email);

            // Siempre mostrar el mismo mensaje (evitar enumeración de usuarios)
            $msg = 'Si ese correo está registrado, recibirás un enlace en breve. Revisa también tu carpeta de spam.';

            if ($usuario && $usuario->estado !== 'inactivo') {
                // Token plano (64 chars hexadecimales = 256 bits de entropía)
                $tokenPlano  = bin2hex(random_bytes(32));
                $tokenHashed = hash('sha256', $tokenPlano);
                // $expira se calcula en MySQL (DATE_ADD(NOW(), INTERVAL 24 HOUR))
                // para evitar desfase de timezone entre PHP y MySQL

                $usuarioModel->setRecoveryToken($email, $tokenHashed, null);

                $enlace = BASE_URL . '/index.php?controller=usuario&action=resetear&token=' . urlencode($tokenPlano);

                $asunto = 'Recupera tu contraseña – ' . (defined('APP_NAME') ? APP_NAME : 'CRM');
                $cuerpo = '
<!doctype html>
<html lang="es"><body style="font-family:sans-serif;background:#f0f2f8;padding:30px;">
  <div style="max-width:480px;margin:auto;background:#fff;border-radius:12px;padding:32px;border:1px solid #e4e8f0;">
    <h2 style="color:#1e40af;margin-top:0;">Recuperar contraseña</h2>
    <p>Hola <strong>' . htmlspecialchars($usuario->nombre) . '</strong>,</p>
    <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta.</p>
    <p style="margin:28px 0;">
      <a href="' . $enlace . '"
         style="background:#2563eb;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;">
        Restablecer contraseña
      </a>
    </p>
    <p style="color:#64748b;font-size:0.88rem;">
      Este enlace expira en <strong>24 horas</strong>.<br>
      Si no solicitaste este cambio, ignora este correo.
    </p>
    <hr style="border:none;border-top:1px solid #e4e8f0;margin:24px 0;">
    <p style="color:#94a3b8;font-size:0.8rem;">
      ' . (defined('APP_NAME') ? APP_NAME : 'CRM') . ' &copy; ' . date('Y') . '
    </p>
  </div>
</body></html>';

                Mailer::enviarRecuperacion($email, $asunto, $cuerpo);
            }

            $this->view('usuarios/recuperar', ['mensaje' => $msg, 'tipo' => 'exito']);
            return;
        }

        $this->view('usuarios/recuperar', ['mensaje' => null, 'tipo' => null]);
    }

    /**
     * Paso 2: el usuario hace clic en el enlace del email.
     * - GET  → validar token y mostrar formulario de nueva contraseña
     * - POST → guardar nueva contraseña y limpiar token
     */
    public function resetear()
    {
        $tokenPlano  = trim($this->get('token') ?: $this->post('token'));
        $tokenHashed = $tokenPlano ? hash('sha256', $tokenPlano) : null;

        $usuarioModel = new Usuario();
        $usuario      = $tokenHashed ? $usuarioModel->findByRecoveryToken($tokenHashed) : null;

        if ($this->isPost()) {
            if (!$usuario) {
                $this->view('usuarios/resetear', [
                    'usuario'    => null,
                    'tokenPlano' => $tokenPlano,
                    'error'      => 'El enlace es inválido o ha expirado. Solicita uno nuevo.',
                ]);
                return;
            }

            $nueva    = $this->post('password');
            $confirma = $this->post('password_confirma');

            if (strlen($nueva) < 8) {
                $this->view('usuarios/resetear', [
                    'usuario'    => $usuario,
                    'tokenPlano' => $tokenPlano,
                    'error'      => 'La contraseña debe tener al menos 8 caracteres.',
                ]);
                return;
            }

            if ($nueva !== $confirma) {
                $this->view('usuarios/resetear', [
                    'usuario'    => $usuario,
                    'tokenPlano' => $tokenPlano,
                    'error'      => 'Las contraseñas no coinciden.',
                ]);
                return;
            }

            $usuarioModel->cambiarPassword($usuario->id, $nueva);
            $usuarioModel->clearRecoveryToken($usuario->id);

            // Redirigir al login con mensaje flash de éxito
            $_SESSION['flash_exito'] = 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.';
            $this->redirect(BASE_URL . '/index.php?controller=usuario&action=login');
            return;
        }

        // GET: mostrar formulario sólo si el token es válido
        $error = null;
        if (!$usuario) {
            $error = 'El enlace es inválido o ha expirado. Solicita uno nuevo.';
        }

        $this->view('usuarios/resetear', [
            'usuario'    => $usuario,
            'tokenPlano' => $tokenPlano,
            'error'      => $error,
        ]);
    }
}
