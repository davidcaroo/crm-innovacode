<?php

/**
 * ClienteController
 * Controlador para todas las operaciones relacionadas con clientes
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Venta.php';

class ClienteController extends BaseController
{
    private $clienteModel;
    private $ventaModel;

    public function __construct()
    {
        $this->clienteModel = new Cliente();
        $this->ventaModel = new Venta();
    }

    /**
     * Mostrar listado de clientes con búsqueda
     */
    public function index()
    {
        try {
            $busqueda = $this->get('nombre', '');

            if (!empty($busqueda)) {
                $clientes = $this->clienteModel->buscarPorNombre($busqueda);
            } else {
                $clientes = $this->clienteModel->obtenerTodosOrdenados();
            }

            $this->view('clientes/index', [
                'clientes' => $clientes,
                'busqueda' => $busqueda
            ]);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Mostrar formulario para agregar cliente
     */
    public function crear()
    {
        $this->view('clientes/crear', [
            'departamentos' => $this->getDepartamentos()
        ]);
    }

    /**
     * Guardar nuevo cliente
     */
    public function guardar()
    {
        if (!$this->isPost()) {
            $this->redirect('index.php?controller=cliente&action=index');
            return;
        }

        try {
            $nombre = $this->post('nombre');
            $edad = $this->post('edad');
            $departamento = $this->post('departamento');

            $resultado = $this->clienteModel->agregar($nombre, $edad, $departamento);

            if ($resultado) {
                $this->redirect('index.php?controller=cliente&action=index');
            } else {
                $this->error("No se pudo registrar el cliente");
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Mostrar formulario para editar cliente
     */
    public function editar()
    {
        try {
            $id = $this->validateId($this->get('id'));
            $cliente = $this->clienteModel->find($id);

            if (!$cliente) {
                $this->error("Cliente no encontrado", 404);
            }

            $this->view('clientes/editar', [
                'cliente' => $cliente,
                'departamentos' => $this->getDepartamentos()
            ]);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Actualizar cliente existente
     */
    public function actualizar()
    {
        if (!$this->isPost()) {
            $this->redirect('index.php?controller=cliente&action=index');
            return;
        }

        try {
            $id = $this->validateId($this->post('id'));
            $nombre = $this->post('nombre');
            $edad = $this->post('edad');
            $departamento = $this->post('departamento');

            $resultado = $this->clienteModel->actualizar($id, $nombre, $edad, $departamento);

            $this->redirect('index.php?controller=cliente&action=index');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Eliminar un cliente
     */
    public function eliminar()
    {
        try {
            $id = $this->validateId($this->get('id'));

            // Nota: Las ventas se eliminan automáticamente por CASCADE en la BD
            $resultado = $this->clienteModel->delete($id);

            $this->redirect('index.php?controller=cliente&action=index');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Mostrar dashboard individual de un cliente
     */
    public function dashboard()
    {
        try {
            $id = $this->validateId($this->get('id'));
            $cliente = $this->clienteModel->find($id);

            if (!$cliente) {
                $this->error("Cliente no encontrado", 404);
            }

            // Obtener estadísticas del cliente
            $totalVentas = $this->ventaModel->totalPorCliente($id);
            $totalMes = $this->ventaModel->totalPorClienteUltimoMes($id);
            $totalAnio = $this->ventaModel->totalPorClienteAnioActual($id);
            $totalAniosAnteriores = $this->ventaModel->totalPorClienteAniosAnteriores($id);
            $ventas = $this->ventaModel->obtenerPorCliente($id);

            $this->view('clientes/dashboard', [
                'cliente' => $cliente,
                'totalVentas' => $totalVentas,
                'totalMes' => $totalMes,
                'totalAnio' => $totalAnio,
                'totalAniosAnteriores' => $totalAniosAnteriores,
                'ventas' => $ventas
            ]);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
