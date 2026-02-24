<?php

/**
 * VentaController - Gestiona ventas vinculadas a empresas ganadas
 */
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../models/Empresa.php';
require_once __DIR__ . '/../models/Notificacion.php';

class VentaController extends BaseController
{
    private $ventaModel;
    private $empresaModel;

    public function __construct()
    {
        $this->ventaModel  = new Venta();
        $this->empresaModel = new Empresa();
    }

    public function index()
    {
        try {
            $usuario_id_filtro = in_array($_SESSION['usuario_rol'], ['admin', 'superadmin']) ? null : $_SESSION['usuario_id'];
            $ventas = $this->ventaModel->obtenerConEmpresa($usuario_id_filtro);

            // Solo empresas en etapa ganado pueden tener venta registrada
            $todasEmpresas = (in_array($_SESSION['usuario_rol'], ['admin', 'superadmin']))
                ? $this->empresaModel->todasAdmin()
                : $this->empresaModel->todasPorUsuario($_SESSION['usuario_id']);

            $empresasGanadas = array_filter($todasEmpresas, fn($e) => $e->etapa_venta === 'ganado');

            $this->view('ventas/index', [
                'ventas'          => $ventas,
                'empresasGanadas' => array_values($empresasGanadas),
                'totalVentas'     => $this->ventaModel->totalGeneral(),
            ]);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function guardar()
    {
        if (!$this->isPost()) {
            $this->redirect('index.php?controller=venta&action=index');
            return;
        }
        try {
            $empresa_id  = $this->post('empresa_id');
            $monto       = $this->post('monto');
            $fecha       = $this->post('fecha');
            $usuario_id  = $_SESSION['usuario_id'];

            $this->ventaModel->agregar($empresa_id, $monto, $fecha, $usuario_id);

            // Emitir notificación in-app a admins/superadmins
            $empresa = $this->empresaModel->obtener($empresa_id, null);
            $nombre  = $empresa ? $empresa->razon_social : 'Empresa';
            $montoFmt = '$' . number_format((float)$monto, 2);
            Notificacion::emitir(
                'venta_ganada',
                "Venta registrada: {$nombre}",
                "{$_SESSION['usuario_nombre']} registró una venta de {$montoFmt} el {$fecha}.",
                BASE_URL . '/index.php?controller=venta&action=index'
            );

            $this->redirect('index.php?controller=venta&action=index');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function eliminar()
    {
        try {
            $id = $this->validateId($this->get('id'));
            $usuario_id = in_array($_SESSION['usuario_rol'], ['admin', 'superadmin']) ? null : $_SESSION['usuario_id'];

            $this->ventaModel->delete($id, $usuario_id);
            $this->redirect('index.php?controller=venta&action=index');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
