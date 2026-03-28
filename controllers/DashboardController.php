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
            $empresasUltimos30Dias = $this->empresaModel->contarGestionesUltimosDias(30, $u_id);
            $empresasAnioActual = $this->empresaModel->contarGestionesAnioActual($u_id);

            // Gráfica global: ganadas por mes de todos los usuarios
            $empresasGanadas = $this->empresaModel->empresasGanadasPorMes(null);
            $empresasPerdidas = $this->empresaModel->empresasPerdidasPorMes(null);
            $totalVentas = $this->empresaModel->contarGestionesGanadas($u_id);

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
                'empresasGanadas' => $empresasGanadas,
                'empresasPerdidas' => $empresasPerdidas
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
