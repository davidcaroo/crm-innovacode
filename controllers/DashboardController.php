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
            // Estadísticas de empresas
            $totalEmpresas = $this->empresaModel->count();
            $empresasUltimos30Dias = $this->empresaModel->contarUltimosDias(30);
            $empresasAnioActual = $this->empresaModel->contarAnioActual();

            // Estadísticas de ventas (empresas ganadas)
            $empresasGanadas = $this->empresaModel->empresasGanadasPorMes();
            $totalVentas = 0;
            foreach ($empresasGanadas as $item) {
                $totalVentas += $item->total;
            }

            // Datos para gráficos
            $empresasPorDepartamento = $this->empresaModel->contarPorDepartamento();
            $empresasPorActividad = $this->empresaModel->contarPorActividadEconomica();
            $empresasPorEtapa = $this->empresaModel->contarPorEtapa();

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
