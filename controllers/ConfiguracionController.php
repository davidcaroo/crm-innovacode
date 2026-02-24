<?php
// controllers/ConfiguracionController.php (solo admin/superadmin)
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Configuracion.php';

class ConfiguracionController extends BaseController
{
    public function editar()
    {
        // Solo admin/superadmin
        if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_rol'], ['admin', 'superadmin'])) {
            header('Location: index.php');
            exit;
        }
        $mensaje = "";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $host = trim($_POST['smtp_host'] ?? '');
            $port = intval($_POST['smtp_port'] ?? 0);
            $user = trim($_POST['smtp_user'] ?? '');
            $pass = trim($_POST['smtp_pass'] ?? '');
            $notif = isset($_POST['notificaciones_ganado']) ? true : false;

            Configuracion::setSMTP($host, $port, $user, $pass, $notif);
            $mensaje = "Configuración actualizada correctamente.";
        }
        $smtp = Configuracion::getSMTP();
        $this->view('configuracion/editar', ['smtp' => $smtp, 'mensaje' => $mensaje]);
    }
}
