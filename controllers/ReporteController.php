<?php
// controllers/ReporteController.php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Reporte.php';

class ReporteController extends BaseController
{
    private $reporteModel;

    public function __construct()
    {
        $this->reporteModel = new Reporte();
    }

    public function index()
    {
        $usuario_id = $_SESSION['usuario_rol'] === 'usuario' ? $_SESSION['usuario_id'] : null;

        $stats = [
            'ventas_mes'   => $this->reporteModel->ventasMensuales($usuario_id),
            'conversion'   => $this->reporteModel->conversionRates($usuario_id),
            'actividades'  => $this->reporteModel->resumenActividades($usuario_id),
            'ranking'      => ($_SESSION['usuario_rol'] !== 'usuario') ? $this->reporteModel->rankingVendedores() : []
        ];

        // Preparar datos para los charts
        $labelsVentas = [];
        $dataVentas   = [];
        $meses = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];

        foreach ($stats['ventas_mes'] as $v) {
            $labelsVentas[] = $meses[$v->mes - 1];
            $dataVentas[]   = $v->total;
        }

        $this->view('reportes/index', [
            'stats'        => $stats,
            'labelsVentas' => $labelsVentas,
            'dataVentas'   => $dataVentas
        ]);
    }
}
