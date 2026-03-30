<?php

/**
 * DashboardController
 * Controlador para el dashboard general con estadísticas y gráficos
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Empresa.php';
require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../models/Reporte.php';

class DashboardController extends BaseController
{
    private $empresaModel;
    private $ventaModel;
    private $reporteModel;

    public function __construct()
    {
        $this->empresaModel = new Empresa();
        $this->ventaModel = new Venta();
        $this->reporteModel = new Reporte();
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

            // Nuevas métricas avanzadas de pipeline (mutuamente excluyentes)
            $filtrosReporte = [];
            if ($u_id !== null) {
                $filtrosReporte['usuario_id'] = $u_id;
            }
            $reporteAvanzado = $this->reporteModel->resumenGlobalComercialUsuarios($filtrosReporte);
            
            // Consolidar a una sola fila sumando todos los usuarios si es admin
            $consolidadoPipeline = [
                'prospectado' => 0, // este dato no viene del reporte, se calcula restando
                'investigacion' => 0,
                'total_contactados' => 0,
                'contacto_interesado' => 0,
                'estudio_necesidades' => 0,
                'oferta_servicios' => 0,
                'seguimiento_oferta' => 0,
                'perdidos' => 0,
                'cierre_exitoso' => 0
            ];
            
            foreach ($reporteAvanzado as $r) {
                $consolidadoPipeline['investigacion'] += (int)$r->investigacion;
                $consolidadoPipeline['total_contactados'] += (int)$r->total_contactados;
                $consolidadoPipeline['contacto_interesado'] += (int)$r->contacto_interesado;
                $consolidadoPipeline['estudio_necesidades'] += (int)$r->estudio_necesidades;
                $consolidadoPipeline['oferta_servicios'] += (int)$r->oferta_servicios;
                $consolidadoPipeline['seguimiento_oferta'] += (int)$r->seguimiento_oferta;
                $consolidadoPipeline['perdidos'] += (int)$r->perdidos;
                $consolidadoPipeline['cierre_exitoso'] += (int)$r->cierre_exitoso;
            }
            
            // Prospectados: Total de empresas menos los "Total Contactados"
            $consolidadoPipeline['prospectado'] = max(0, $totalEmpresas - $consolidadoPipeline['total_contactados']);

            $this->view('dashboard/index', [
                'totalEmpresas' => $totalEmpresas,
                'empresasUltimos30Dias' => $empresasUltimos30Dias,
                'empresasAnioActual' => $empresasAnioActual,
                'totalVentas' => $totalVentas,
                'empresasPorDepartamento' => $empresasPorDepartamento,
                'empresasPorActividad' => $empresasPorActividad,
                'empresasPorEtapa' => $empresasPorEtapa,
                'consolidadoPipeline' => $consolidadoPipeline,
                'empresasGanadas' => $empresasGanadas,
                'empresasPerdidas' => $empresasPerdidas
            ]);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
