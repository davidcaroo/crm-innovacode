<?php
// controllers/ConfiguracionController.php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Configuracion.php';
require_once __DIR__ . '/../models/Integracion.php';
require_once __DIR__ . '/../models/Notificacion.php';
require_once __DIR__ . '/../models/Mailer.php';

class ConfiguracionController extends BaseController
{
    private function soloAdmins(): void
    {
        if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_rol'], ['admin', 'superadmin'])) {
            $this->redirect('index.php?controller=dashboard&action=index');
        }
    }

    // ----------------------------------------------------------
    // GET: Vista principal con 3 tabs
    // ----------------------------------------------------------
    public function editar(): void
    {
        $this->soloAdmins();

        $integracionModel = new Integracion();

        $smtp          = Configuracion::getSMTP();
        $integraciones = $integracionModel->getAll();
        $eventos       = Notificacion::eventosDisponibles();
        $roles         = ['superadmin', 'admin', 'usuario'];
        $prefsRoles    = [];
        foreach ($roles as $rol) {
            $prefsRoles[$rol] = Notificacion::getPreferenciasRol($rol);
        }

        $tab = in_array($_GET['tab'] ?? '', ['smtp', 'integraciones', 'notificaciones']) ? $_GET['tab'] : 'smtp';

        $this->view('configuracion/index', compact(
            'smtp',
            'integraciones',
            'eventos',
            'prefsRoles',
            'roles',
            'tab'
        ));
    }

    public function index(): void
    {
        $this->editar();
    }

    // ----------------------------------------------------------
    // POST: Guardar SMTP
    // ----------------------------------------------------------
    public function guardarSmtp(): void
    {
        $this->soloAdmins();
        if (!$this->isPost()) {
            $this->redirect('index.php?controller=configuracion&action=editar');
            return;
        }
        $host  = trim($this->post('smtp_host')  ?? '');
        $port  = intval($this->post('smtp_port') ?? 587);
        $user  = trim($this->post('smtp_user')  ?? '');
        $pass  = trim($this->post('smtp_pass')  ?? '');
        $notif = isset($_POST['notificaciones_ganado']);
        Configuracion::setSMTP($host, $port, $user, $pass, $notif);
        $this->redirect('index.php?controller=configuracion&action=editar&tab=smtp&ok=smtp');
    }

    // ----------------------------------------------------------
    // POST: Guardar integración
    // ----------------------------------------------------------
    public function guardarIntegracion(): void
    {
        $this->soloAdmins();
        if (!$this->isPost()) {
            $this->redirect('index.php?controller=configuracion&action=editar&tab=integraciones');
            return;
        }
        $slug   = preg_replace('/[^a-z0-9_]/', '', $this->post('slug') ?? '');
        $estado = ($this->post('estado') === 'activa') ? 'activa' : 'inactiva';
        $campos = [];
        foreach ($_POST as $key => $val) {
            if (strpos($key, 'campo_') === 0) {
                $campos[substr($key, 6)] = trim($val);
            }
        }
        $integracionModel = new Integracion();
        $integracionModel->guardar($slug, $campos, $estado);
        $this->redirect('index.php?controller=configuracion&action=editar&tab=integraciones&ok=int');
    }

    // ----------------------------------------------------------
    // POST: Toggle integración
    // ----------------------------------------------------------
    public function toggleIntegracion(): void
    {
        $this->soloAdmins();
        $id = intval($this->get('id') ?? 0);
        if ($id > 0) {
            $model = new Integracion();
            $model->toggle($id);
        }
        $this->redirect('index.php?controller=configuracion&action=editar&tab=integraciones');
    }

    // ----------------------------------------------------------
    // POST: Guardar preferencias de notificación por rol
    // ----------------------------------------------------------
    public function guardarNotificaciones(): void
    {
        $this->soloAdmins();
        if (!$this->isPost()) {
            $this->redirect('index.php?controller=configuracion&action=editar&tab=notificaciones');
            return;
        }
        $eventos = array_keys(Notificacion::eventosDisponibles());
        $roles   = ['superadmin', 'admin', 'usuario'];
        foreach ($roles as $rol) {
            foreach ($eventos as $evento) {
                $email = isset($_POST["notif_{$rol}_{$evento}_email"]) ? true : false;
                $inapp = isset($_POST["notif_{$rol}_{$evento}_inapp"]) ? true : false;
                Notificacion::setPreferenciasRol($rol, $evento, $email, $inapp);
            }
        }
        $this->redirect('index.php?controller=configuracion&action=editar&tab=notificaciones&ok=notif');
    }

    // ----------------------------------------------------------
    // AJAX: Probar SMTP
    // ----------------------------------------------------------
    public function probarSmtp(): void
    {
        $this->soloAdmins();
        header('Content-Type: application/json');
        $result = Mailer::probarConexion();
        echo json_encode($result);
        exit;
    }
}
