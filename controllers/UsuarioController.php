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

                // Verificar si es el primer login y forzar cambio de contraseña
                if (isset($usuario->primer_login) && $usuario->primer_login == 1) {
                    $_SESSION['cambio_password_obligatorio'] = true;
                    $this->redirect(url('usuario/cambiarPasswordObligatorio'));
                    return;
                }

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

        $nombre = $this->post('nombre');
        $email = $this->post('email');
        $rol = $this->post('rol') ?: 'usuario';

        // Generar contraseña temporal automática
        $passwordTemporal = Usuario::generarPasswordTemporal(12);

        // DEBUG: Log temporal (comentar en producción)
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log("DEBUG - Nuevo usuario: Email=$email, Password temporal=$passwordTemporal");
        }

        $usuarioModel = new Usuario();
        $resultado = $usuarioModel->crear($nombre, $email, $passwordTemporal, $rol, 1);

        if (!$resultado) {
            error_log("ERROR: No se pudo crear el usuario $email en la base de datos");
            $this->redirect(url('usuario/lista', ['error' => 'creation_failed']));
            return;
        }

        // Enviar email con credenciales
        $emailEnviado = Mailer::enviarCredencialesNuevoUsuario($nombre, $email, $passwordTemporal);

        if (!$emailEnviado && defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log("ADVERTENCIA: No se pudo enviar el email de credenciales a $email");
        }

        $this->redirect(url('usuario/lista', ['success' => 'user_created']));
    }

    public function editarUsuario()
    {
        $this->requireAdmin();
        $id           = $this->get('id');
        $usuarioModel = new Usuario();
        $usuario      = $usuarioModel->obtener($id);
        // Proteger al superadmin: nadie puede editar su perfil excepto él mismo
        if ($usuario && $usuario->rol === 'superadmin' && $_SESSION['usuario_id'] != $id) {
            $this->redirect(url('usuario/lista', ['error' => 'superadmin_protected']));
            return;
        }
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
        // Proteger al superadmin: nadie puede modificar su cuenta excepto él mismo
        $objetivo = $usuarioModel->obtener($id);
        if ($objetivo && $objetivo->rol === 'superadmin' && $_SESSION['usuario_id'] != $id) {
            $this->redirect(url('usuario/lista', ['error' => 'superadmin_protected']));
            return;
        }
        $rol    = $this->post('rol');
        $estado = $this->post('estado') ?: 'activo';
        // Preservar siempre el rol superadmin aunque alguien manipule el formulario
        if ($objetivo && $objetivo->rol === 'superadmin') {
            $rol = 'superadmin';
        }
        $usuarioModel->actualizar($id, $this->post('nombre'), $this->post('email'), $rol ?: 'usuario', $estado);

        $nueva = $this->post('password');
        if ($nueva) {
            $usuarioModel->cambiarPassword($id, $nueva);
        }
        $this->redirect(url('usuario/lista', ['success' => 'user_updated']));
    }

    public function eliminarUsuario()
    {
        $this->requireAdmin();
        $id = $this->get('id');
        if ($id && $id != $_SESSION['usuario_id']) {
            $usuarioModel = new Usuario();
            // Proteger al superadmin: nunca puede ser eliminado
            $objetivo = $usuarioModel->obtener($id);
            if ($objetivo && $objetivo->rol === 'superadmin') {
                $this->redirect(url('usuario/lista', ['error' => 'superadmin_protected']));
                return;
            }
            $usuarioModel->eliminar($id);
        }
        $this->redirect(url('usuario/lista', ['success' => 'user_deleted']));
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

                $enlace = url('usuario/resetear', ['token' => $tokenPlano]);

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

    /* ============================
       Cambio Obligatorio de Contraseña (Primer Login)
       ============================ */

    /**
     * Muestra el formulario de cambio obligatorio de contraseña
     * Solo accesible si el usuario tiene primer_login = 1
     */
    public function cambiarPasswordObligatorio()
    {
        // Verificar que el usuario esté logueado
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect(url('usuario/login'));
            return;
        }

        // Verificar que realmente necesite cambiar la contraseña
        if (!isset($_SESSION['cambio_password_obligatorio'])) {
            $this->redirect(BASE_URL . '/index.php');
            return;
        }

        // Mostrar el formulario sin el layout normal (sin sidebar)
        $this->view('usuarios/cambiar_password_obligatorio', [
            'usuario_nombre' => $_SESSION['usuario_nombre']
        ], false); // false = sin layout
    }

    /**
     * Procesa el cambio obligatorio de contraseña
     */
    public function procesarCambioObligatorio()
    {
        if (!$this->isPost() || !isset($_SESSION['usuario_id'])) {
            $this->redirect(url('usuario/login'));
            return;
        }

        if (!isset($_SESSION['cambio_password_obligatorio'])) {
            $this->redirect(BASE_URL . '/index.php');
            return;
        }

        try {
            $passwordActual = $this->post('password_actual');
            $passwordNueva = $this->post('password_nueva');
            $passwordConfirma = $this->post('password_confirma');

            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->obtenerConPassword($_SESSION['usuario_id']);

            // Validar que el usuario existe y tiene password
            if (!$usuario || empty($usuario->password)) {
                $this->view('usuarios/cambiar_password_obligatorio', [
                    'usuario_nombre' => $_SESSION['usuario_nombre'],
                    'error' => 'Error al cargar los datos del usuario. Contacta al administrador.'
                ], false);
                return;
            }

            // Validar contraseña actual
            if (!password_verify($passwordActual, $usuario->password)) {
                $this->view('usuarios/cambiar_password_obligatorio', [
                    'usuario_nombre' => $_SESSION['usuario_nombre'],
                    'error' => 'La contraseña temporal es incorrecta. Verifica las mayúsculas/minúsculas.'
                ], false);
                return;
            }

            // Validar longitud mínima
            if (strlen($passwordNueva) < 8) {
                $this->view('usuarios/cambiar_password_obligatorio', [
                    'usuario_nombre' => $_SESSION['usuario_nombre'],
                    'error' => 'La nueva contraseña debe tener al menos 8 caracteres.'
                ], false);
                return;
            }

            // Validar que coincidan
            if ($passwordNueva !== $passwordConfirma) {
                $this->view('usuarios/cambiar_password_obligatorio', [
                    'usuario_nombre' => $_SESSION['usuario_nombre'],
                    'error' => 'Las contraseñas no coinciden.'
                ], false);
                return;
            }

            // Validar que no sea igual a la temporal
            if ($passwordActual === $passwordNueva) {
                $this->view('usuarios/cambiar_password_obligatorio', [
                    'usuario_nombre' => $_SESSION['usuario_nombre'],
                    'error' => 'La nueva contraseña debe ser diferente a la temporal.'
                ], false);
                return;
            }

            // Actualizar contraseña y marcar primer_login = 0
            $resultado = $usuarioModel->cambiarPassword($usuario->id, $passwordNueva, true);

            if (!$resultado) {
                throw new Exception('No se pudo actualizar la contraseña en la base de datos');
            }

            // Limpiar flag de sesión
            unset($_SESSION['cambio_password_obligatorio']);

            // Redirigir al dashboard con mensaje de éxito
            $this->redirect(url('dashboard/index', ['success' => 'password_changed']));
        } catch (Exception $e) {
            error_log("ERROR en procesarCambioObligatorio: " . $e->getMessage());
            $this->view('usuarios/cambiar_password_obligatorio', [
                'usuario_nombre' => $_SESSION['usuario_nombre'],
                'error' => 'Ha ocurrido un error inesperado. Por favor, intenta de nuevo o contacta al administrador.'
            ], false);
        }
    }
}
