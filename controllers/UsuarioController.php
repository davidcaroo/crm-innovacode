<?php
require_once __DIR__ . '/../models/Usuario.php';
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
        $usuarioModel->actualizar(
            $id,
            $this->post('nombre'),
            $this->post('email'),
            $this->post('rol') ?: 'usuario'
        );
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
}
