<?php
require_once __DIR__ . '/../models/Trazabilidad.php';
require_once __DIR__ . '/../models/Empresa.php';
require_once __DIR__ . '/BaseController.php';

class TrazabilidadController extends BaseController
{
    private function validarPropiedadEmpresa($empresa_id)
    {
        $usuario_id = in_array($_SESSION['usuario_rol'], ['admin', 'superadmin']) ? null : $_SESSION['usuario_id'];
        $empresaModel = new Empresa();
        $empresa = $empresaModel->obtener($empresa_id, $usuario_id);
        if (!$empresa) {
            $this->redirect(url('empresa/index'));
            exit;
        }
        return $empresa;
    }

    public function index()
    {
        $empresa_id = $this->get('empresa_id');
        if (!$empresa_id) {
            $this->redirect(url('empresa/index'));
            return;
        }

        $empresa = $this->validarPropiedadEmpresa($empresa_id);

        $trazabilidadModel = new Trazabilidad();
        $historial       = $trazabilidadModel->historialPorEmpresa($empresa_id);
        $this->view('trazabilidad/index', [
            'historial'  => $historial,
            'empresa_id' => $empresa_id,
            'empresa'    => $empresa,
        ]);
    }

    public function historial()
    {
        $trazabilidadModel = new Trazabilidad();
        $usuario_id = in_array($_SESSION['usuario_rol'], ['admin', 'superadmin']) ? null : $_SESSION['usuario_id'];
        $historial = $trazabilidadModel->historialGlobal($usuario_id);
        $this->view('trazabilidad/global', ['historial' => $historial]);
    }

    public function registrar()
    {
        $empresa_id = $this->get('empresa_id') ?? $this->post('empresa_id');
        $this->validarPropiedadEmpresa($empresa_id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'empresa_id'     => $this->post('empresa_id'),
                'usuario_id'     => $_SESSION['usuario_id'],
                'etapa_venta'    => $this->post('etapa_venta'),
                'tipo_actividad' => $this->post('tipo_actividad'),
                'observaciones'  => $this->post('observaciones'),
            ];
            $trazabilidadModel = new Trazabilidad();
            $trazabilidadModel->registrar($data);

            // Actualizar etapa de la empresa si cambió
            $empresaModel = new Empresa();
            $empresaModel->actualizarEtapa($data['empresa_id'], $data['etapa_venta']);

            $this->redirect(url('trazabilidad/index', ['empresa_id' => $data['empresa_id']]));
        } else {
            $empresaModel = new Empresa();
            $empresa      = $empresaModel->obtener($empresa_id); // Ya validado arriba
            $this->view('trazabilidad/registrar', [
                'empresa_id' => $empresa_id,
                'empresa'    => $empresa,
            ]);
        }
    }
}
