<?php

/**
 * DashboardController
 * Controlador para el dashboard general con estadísticas y gráficos
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Empresa.php';
require_once __DIR__ . '/../models/Venta.php';

class DashboardController extends BaseController
{
    private $empresaModel;
    private $ventaModel;

    public function __construct()
    {
        $this->empresaModel = new Empresa();
        $this->ventaModel = new Venta();
    }

    /**
     * Mostrar dashboard general con todas las estadísticas
     */
    public function index()
    {
        try {
            $u_id = in_array($_SESSION['usuario_rol'], ['admin', 'superadmin']) ? null : $_SESSION['usuario_id'];

            // Estadísticas de empresas
            $totalEmpresas = $this->empresaModel->count($u_id);
            $empresasUltimos30Dias = $this->empresaModel->contarUltimosDias(30, $u_id);
            $empresasAnioActual = $this->empresaModel->contarAnioActual($u_id);

            // Estadísticas de ventas (empresas ganadas)
            $empresasGanadas = $this->empresaModel->empresasGanadasPorMes($u_id);
            $totalVentas = 0;
            foreach ($empresasGanadas as $item) {
                $totalVentas += $item->total;
            }

            // Datos para gráficos
            $empresasPorDepartamento = $this->empresaModel->contarPorDepartamento($u_id);
            $empresasPorActividad = $this->empresaModel->contarPorActividadEconomica($u_id);
            $empresasPorEtapa = $this->empresaModel->contarPorEtapa($u_id);

            $this->view('dashboard/index', [
                'totalEmpresas' => $totalEmpresas,
                'empresasUltimos30Dias' => $empresasUltimos30Dias,
                'empresasAnioActual' => $empresasAnioActual,
                'totalVentas' => $totalVentas,
                'empresasPorDepartamento' => $empresasPorDepartamento,
                'empresasPorActividad' => $empresasPorActividad,
                'empresasPorEtapa' => $empresasPorEtapa,
                'empresasGanadas' => $empresasGanadas
            ]);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Página de créditos
     */
    public function creditos()
    {
        $this->view('dashboard/creditos', []);
    }
}
