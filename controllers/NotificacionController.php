<?php
// controllers/NotificacionController.php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Notificacion.php';

class NotificacionController extends BaseController
{
    private Notificacion $model;

    public function __construct()
    {
        $this->model = new Notificacion();
    }

    // ----------------------------------------------------------
    // Vista panel de notificaciones del usuario actual
    // ----------------------------------------------------------
    public function index(): void
    {
        $usuario_id    = $_SESSION['usuario_id'];
        $notificaciones = $this->model->getTodas($usuario_id, 60);
        $this->model->marcarTodasLeidas($usuario_id);

        $this->view('notificaciones/index', compact('notificaciones'));
    }

    // ----------------------------------------------------------
    // AJAX GET: Conteo de no leídas → { count: N }
    // ----------------------------------------------------------
    public function conteo(): void
    {
        header('Content-Type: application/json');
        $count = $this->model->conteoNoLeidas($_SESSION['usuario_id']);
        echo json_encode(['count' => $count]);
        exit;
    }

    // ----------------------------------------------------------
    // AJAX POST: Marcar una notificación como leída → { ok: true }
    // ----------------------------------------------------------
    public function marcarLeida(): void
    {
        header('Content-Type: application/json');
        $id         = intval($this->get('id') ?? 0);
        $usuario_id = $_SESSION['usuario_id'];
        $ok = $this->model->marcarLeida($id, $usuario_id);
        echo json_encode(['ok' => $ok]);
        exit;
    }

    // ----------------------------------------------------------
    // POST: Marcar todas como leídas → redirige
    // ----------------------------------------------------------
    public function marcarTodas(): void
    {
        $this->model->marcarTodasLeidas($_SESSION['usuario_id']);
        $this->redirect('index.php?controller=notificacion&action=index');
    }
}
