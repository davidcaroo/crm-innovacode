<?php
require_once __DIR__ . '/../models/Contacto.php';
require_once __DIR__ . '/../models/Empresa.php';
require_once __DIR__ . '/BaseController.php';

class ContactoController extends BaseController
{
    private function validarPropiedadEmpresa($empresa_id)
    {
        $usuario_id = in_array($_SESSION['usuario_rol'], ['admin', 'superadmin']) ? null : $_SESSION['usuario_id'];
        $empresaModel = new Empresa();
        $empresa = $empresaModel->obtener($empresa_id, $usuario_id);
        if (!$empresa) {
            $this->redirect(BASE_URL . '/index.php?controller=empresa&action=index&error=auth');
            exit;
        }
        return $empresa;
    }

    public function index()
    {
        $empresa_id = $this->get('empresa_id');
        if (!$empresa_id) {
            // Si no hay empresa_id, redirigir al listado de empresas
            $this->redirect(BASE_URL . '/index.php?controller=empresa&action=index');
            return;
        }

        $this->validarPropiedadEmpresa($empresa_id);

        $contactoModel = new Contacto();
        $contactos = $contactoModel->todosPorEmpresa($empresa_id);
        $this->view('contactos/index', ['contactos' => $contactos, 'empresa_id' => $empresa_id]);
    }

    public function crear()
    {
        $empresa_id = $this->get('empresa_id');
        $this->validarPropiedadEmpresa($empresa_id);
        $this->view('contactos/crear', ['empresa_id' => $empresa_id]);
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $empresa_id = $this->post('empresa_id');
            $this->validarPropiedadEmpresa($empresa_id);

            $data = [
                'empresa_id' => $empresa_id,
                'nombre' => $this->post('nombre'),
                'cargo' => $this->post('cargo'),
                'email' => $this->post('email'),
                'telefono' => $this->post('telefono')
            ];
            $contactoModel = new Contacto();
            $contactoModel->crear($data);
            $this->redirect(BASE_URL . '/index.php?controller=contacto&action=index&empresa_id=' . $empresa_id);
        }
    }

    public function editar()
    {
        $id = $this->get('id');
        $contactoModel = new Contacto();
        $contacto = $contactoModel->obtener($id);

        if ($contacto) {
            $this->validarPropiedadEmpresa($contacto->empresa_id);
        }

        $this->view('contactos/editar', ['contacto' => $contacto]);
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $this->post('id');
            $empresa_id = $this->post('empresa_id');

            $this->validarPropiedadEmpresa($empresa_id);

            $data = [
                'nombre' => $this->post('nombre'),
                'cargo' => $this->post('cargo'),
                'email' => $this->post('email'),
                'telefono' => $this->post('telefono')
            ];
            $contactoModel = new Contacto();
            $contactoModel->actualizar($id, $data);
            $this->redirect(BASE_URL . '/index.php?controller=contacto&action=index&empresa_id=' . $empresa_id);
        }
    }

    public function eliminar()
    {
        $id = $this->get('id');
        $empresa_id = $this->get('empresa_id');

        $this->validarPropiedadEmpresa($empresa_id);

        $contactoModel = new Contacto();
        $contactoModel->eliminar($id);
        $this->redirect(BASE_URL . '/index.php?controller=contacto&action=index&empresa_id=' . $empresa_id);
    }
}
