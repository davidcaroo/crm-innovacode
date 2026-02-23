<?php
// controllers/ConfiguracionController.php (solo admin/superadmin)
class ConfiguracionController extends BaseController
{
    public function editar()
    {
        // Solo admin/superadmin
        if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], ['admin', 'superadmin'])) {
            header('Location: index.php');
            exit;
        }
        $mensaje = "";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $host = trim($_POST['smtp_host'] ?? '');
            $port = intval($_POST['smtp_port'] ?? 0);
            $user = trim($_POST['smtp_user'] ?? '');
            $pass = trim($_POST['smtp_pass'] ?? '');
            Configuracion::setSMTP($host, $port, $user, $pass);
            $mensaje = "Configuración actualizada correctamente.";
        }
        $smtp = Configuracion::getSMTP();
        $this->view('configuracion/editar', ['smtp' => $smtp, 'mensaje' => $mensaje]);
    }
}
