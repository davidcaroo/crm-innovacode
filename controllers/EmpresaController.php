<?php
require_once __DIR__ . '/../models/Empresa.php';
require_once __DIR__ . '/../models/Configuracion.php';
require_once __DIR__ . '/../models/Mailer.php';
require_once __DIR__ . '/../models/Notificacion.php';
require_once __DIR__ . '/../models/Trazabilidad.php';
require_once __DIR__ . '/BaseController.php';

class EmpresaController extends BaseController
{
    public function index()
    {
        $empresaModel = new Empresa();
        $buscar = $this->get('buscar');

        $usuario_id = in_array($_SESSION['usuario_rol'], ['admin', 'superadmin']) ? null : $_SESSION['usuario_id'];
        $totalEmpresas = $empresaModel->count($usuario_id);

        $this->view('empresas/index', [
            'totalEmpresas' => $totalEmpresas,
            'buscar' => $buscar ?? '',
        ]);
    }

    public function crear()
    {
        $this->view('empresas/crear');
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $aplicaNueva = strtoupper(trim((string)$this->post('aplica')));
            if (!in_array($aplicaNueva, ['SI', 'NO'], true)) {
                $this->redirect(BASE_URL . '/index.php?controller=empresa&action=crear&error=aplica_required');
                return;
            }

            $etapaNueva = ($aplicaNueva === 'NO') ? 'perdido' : 'contactado';

            $data = [
                'razon_social' => $this->post('razon_social'),
                'dpto' => $this->post('dpto'),
                'ciudad' => $this->post('ciudad'),
                'actividad_economica' => $this->post('actividad_economica'),
                'correo_comercial' => $this->post('correo_comercial'),
                'aplica' => $aplicaNueva,
                'etapa_venta' => $etapaNueva,
                'observaciones' => $this->post('observaciones'),
                'usuario_id' => $_SESSION['usuario_id']
            ];
            $empresaModel = new Empresa();
            $empresaModel->crear($data);

            // Notificación in-app: nueva empresa creada
            Notificacion::emitir(
                'empresa_creada',
                'Nueva empresa: ' . $data['razon_social'],
                "{$_SESSION['usuario_nombre']} creó la empresa \"{$data['razon_social']}\" en etapa " . ucfirst($data['etapa_venta']) . ".",
                BASE_URL . '/index.php?controller=empresa&action=index'
            );

            $this->redirect(BASE_URL . '/index.php?controller=empresa&action=index');
        }
    }

    public function editar()
    {
        $id = $this->get('id');
        $usuario_id = in_array($_SESSION['usuario_rol'], ['admin', 'superadmin']) ? null : $_SESSION['usuario_id'];

        $empresaModel = new Empresa();
        $empresa = $empresaModel->obtener($id, $usuario_id);

        if (!$empresa) {
            $this->redirect(BASE_URL . '/index.php?controller=empresa&action=index&error=auth');
            return;
        }

        $this->view('empresas/editar', ['empresa' => $empresa]);
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $this->post('id');
            $usuario_id = in_array($_SESSION['usuario_rol'], ['admin', 'superadmin']) ? null : $_SESSION['usuario_id'];

            $empresaModel = new Empresa();

            // Obtener estado anterior para comparar (valida propiedad)
            $empresaAnterior = $empresaModel->obtener($id, $usuario_id);
            if (!$empresaAnterior) {
                $this->redirect(BASE_URL . '/index.php?controller=empresa&action=index&error=auth');
                return;
            }

            $etapaAnterior = $empresaAnterior->etapa_venta ?? '';
            $aplicaInput = strtoupper(trim((string)$this->post('aplica')));
            if (in_array($aplicaInput, ['SI', 'NO'], true)) {
                $aplicaNueva = $aplicaInput;
            }
            else {
                $aplicaNueva = strtoupper(trim((string)($empresaAnterior->aplica ?? '')));
                if (!in_array($aplicaNueva, ['SI', 'NO'], true)) {
                    $aplicaNueva = '';
                }
            }

            $nuevaEtapa = $etapaAnterior;
            if ($aplicaNueva === 'NO') {
                // Regla de negocio: si la empresa no aplica, queda en perdido.
                $nuevaEtapa = 'perdido';
            }
            elseif ($aplicaNueva === 'SI') {
                // Regla de negocio: al confirmar que aplica, pasa a contactado.
                // Se evita degradar etapas avanzadas ya trabajadas.
                if (in_array($etapaAnterior, ['negociacion', 'ganado'], true)) {
                    $nuevaEtapa = $etapaAnterior;
                }
                else {
                    $nuevaEtapa = 'contactado';
                }
            }

            $data = [
                'razon_social' => $this->post('razon_social'),
                'dpto' => $this->post('dpto'),
                'ciudad' => $this->post('ciudad'),
                'actividad_economica' => $this->post('actividad_economica'),
                'correo_comercial' => $this->post('correo_comercial'),
                'aplica' => $aplicaNueva,
                'etapa_venta' => $nuevaEtapa,
                'observaciones' => $this->post('observaciones')
            ];

            $etapaLabels = [
                'prospectado' => 'Prospectado',
                'contactado' => 'Contactado',
                'negociacion' => 'Negociacion',
                'ganado' => 'Ganado',
                'perdido' => 'Perdido',
            ];

            $aplicaAnterior = strtoupper(trim((string)($empresaAnterior->aplica ?? '')));
            $obsAnterior = trim((string)($empresaAnterior->observaciones ?? ''));
            $obsNueva = trim((string)($data['observaciones'] ?? ''));

            $detalleCambios = [];
            if ($nuevaEtapa !== $etapaAnterior) {
                $etapaAnteriorLabel = $etapaLabels[$etapaAnterior] ?? ucfirst((string)$etapaAnterior);
                $etapaNuevaLabel = $etapaLabels[$nuevaEtapa] ?? ucfirst((string)$nuevaEtapa);
                $detalleCambios[] = "Cambio de etapa: {$etapaAnteriorLabel} -> {$etapaNuevaLabel}";
            }

            if ($aplicaNueva !== $aplicaAnterior) {
                $detalleCambios[] = "Cambio de aplica: " . ($aplicaAnterior ?: 'N/A') . " -> " . ($aplicaNueva ?: 'N/A');
            }

            if ($obsNueva !== $obsAnterior) {
                if ($obsNueva !== '') {
                    $detalleCambios[] = "Observacion comercial: {$obsNueva}";
                }
                else {
                    $detalleCambios[] = "Observacion comercial eliminada.";
                }
            }

            $empresaModel->actualizar($id, $data, $usuario_id);

            if (!empty($detalleCambios)) {
                $trazabilidadModel = new Trazabilidad();
                $trazabilidadModel->registrar([
                    'empresa_id' => $id,
                    'usuario_id' => $_SESSION['usuario_id'],
                    'etapa_venta' => $nuevaEtapa,
                    'tipo_actividad' => 'nota',
                    'observaciones' => "Actualizacion comercial de empresa.\n- " . implode("\n- ", $detalleCambios),
                ]);
            }

            // Verificar si cambió a "ganado"
            if ($nuevaEtapa === 'ganado' && $etapaAnterior !== 'ganado') {
                $vendedor = $_SESSION['usuario_nombre'] ?? 'Un vendedor';
                $empresaNombre = $data['razon_social'];

                $asunto = "¡Oportunidad Ganada! - $empresaNombre";
                $cuerpo = "<h3>Venta Cerrada</h3>
                           <p>El vendedor <b>$vendedor</b> ha marcado como <b>GANADA</b> la oportunidad de: <b>$empresaNombre</b>.</p>
                           <p>¡Felicidades!</p>";

                $smtp = Configuracion::getSMTP();
                $destinatario = $smtp['smtp_user'] ?: 'admin@servidor.com';
                Mailer::enviar($destinatario, $asunto, $cuerpo);

                // Notificación in-app: oportunidad ganada
                Notificacion::emitir(
                    'venta_ganada',
                    "¡Oportunidad ganada! {$empresaNombre}",
                    "{$vendedor} cerró la oportunidad de \"{$empresaNombre}\" como GANADA.",
                    BASE_URL . '/index.php?controller=empresa&action=index'
                );
            }
            elseif ($nuevaEtapa !== $etapaAnterior) {
                // Notificación in-app: cambio de etapa
                Notificacion::emitir(
                    'cambio_etapa',
                    'Cambio de etapa: ' . $data['razon_social'],
                    "{$_SESSION['usuario_nombre']} movió \"{$data['razon_social']}\" de " . ucfirst($etapaAnterior) . " → " . ucfirst($nuevaEtapa) . ".",
                    BASE_URL . '/index.php?controller=empresa&action=index'
                );
            }

            $this->redirect(BASE_URL . '/index.php?controller=empresa&action=index');
        }
    }

    public function eliminar()
    {
        $id = $this->get('id');
        $usuario_id = in_array($_SESSION['usuario_rol'], ['admin', 'superadmin']) ? null : $_SESSION['usuario_id'];

        $empresaModel = new Empresa();
        try {
            $empresaModel->eliminar($id, $usuario_id);
            $this->redirect(url('empresa/index', ['success' => 'deleted']));
        }
        catch (Exception $e) {
            $this->redirect(url('empresa/index', ['error' => 'fk_constraint']));
        }
    }

    /**
     * Vista Kanban del pipeline de ventas
     */
    public function pipeline()
    {
        $empresaModel = new Empresa();

        $todasEmpresas = (in_array($_SESSION['usuario_rol'], ['admin', 'superadmin']))
            ? $empresaModel->todasAdmin()
            : $empresaModel->todasPorUsuario($_SESSION['usuario_id']);

        // Agrupar por etapa
        $etapas = ['prospectado' => [], 'contactado' => [], 'negociacion' => [], 'seguimiento' => [], 'ganado' => [], 'perdido' => []];
        foreach ($todasEmpresas as $emp) {
            $etapa = $emp->etapa_venta ?? 'prospectado';
            if (!isset($etapas[$etapa]))
                $etapa = 'prospectado';
            $etapas[$etapa][] = $emp;
        }

        $empresaIds = array_map(function ($e) {
            return (int)($e->id ?? 0);
        }, $todasEmpresas);

        $estadosTrazabilidad = [];
        if (!empty($empresaIds)) {
            $trazabilidadModel = new Trazabilidad();
            $estadosTrazabilidad = $trazabilidadModel->obtenerEstadosFlujoEmpresas($empresaIds);
        }

        $this->view('empresas/pipeline', [
            'etapas' => $etapas,
            'estadosTrazabilidad' => $estadosTrazabilidad,
        ]);
    }

    public function importar()
    {
        $this->view('empresas/importar');
    }

    public function procesarImportacion()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_csv'])) {
            $file = $_FILES['archivo_csv'];

            if ($file['error'] === UPLOAD_ERR_OK) {
                $handle = fopen($file['tmp_name'], 'r');
                // Intentar detectar delimitador (coma o punto y coma)
                $firstLine = fgets($handle);
                $delimiter = (strpos($firstLine, ';') !== false) ? ';' : ',';
                rewind($handle);

                $cabecera = fgetcsv($handle, 1000, $delimiter);

                $filas = [];
                while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                    if (empty($data[0]))
                        continue;

                    // Mapeo detallado basado en la estructura del usuario (A-K):
                    // A(0): Razon Social, B(1): Dpto, C(2): Ciudad, D(3): Actividad, E(4): Correo, F(5): Aplica
                    // G(6): Fuente Tel, H(7): Contacto, I(8): Tel, J(9): Etapa, K(10): Obs

                    $extraObs = "";
                    if (!empty($data[6]))
                        $extraObs .= "Fuente Tel: " . $data[6] . ". ";
                    if (!empty($data[7]))
                        $extraObs .= "Contacto: " . $data[7] . ". ";
                    if (!empty($data[8]))
                        $extraObs .= "Tel: " . $data[8] . ". ";

                    $observaciones = ($data[10] ?? '') . ($extraObs ? "\n--- Info Importada ---\n" . $extraObs : "");

                    $filas[] = [
                        'razon_social' => $data[0] ?? '',
                        'dpto' => $data[1] ?? '',
                        'ciudad' => $data[2] ?? '',
                        'actividad_economica' => $data[3] ?? '',
                        'correo_comercial' => $data[4] ?? '',
                        'aplica' => $data[5] ?? 'Si',
                        'etapa_venta' => $data[9] ?? 'prospectado', // Índice 9 según imagen del usuario
                        'observaciones' => $observaciones // Índice 10 + extras
                    ];
                }
                fclose($handle);

                if (empty($filas)) {
                    $this->redirect(BASE_URL . '/index.php?controller=empresa&action=importar&error=empty');
                    return;
                }

                $empresaModel = new Empresa();
                if ($empresaModel->importarMasivo($filas, $_SESSION['usuario_id'])) {
                    $this->redirect(BASE_URL . '/index.php?controller=empresa&action=index&import=success');
                }
                else {
                    $this->redirect(BASE_URL . '/index.php?controller=empresa&action=importar&error=db');
                }
            }
            else {
                $this->redirect(BASE_URL . '/index.php?controller=empresa&action=importar&error=upload');
            }
        }
    }

    public function datosDataTables()
    {
        $request = $_POST;

        $draw = isset($request['draw']) ? intval($request['draw']) : 1;
        $start = isset($request['start']) ? intval($request['start']) : 0;
        $length = isset($request['length']) ? intval($request['length']) : 10;
        $searchValue = isset($request['search']['value']) ? $request['search']['value'] : '';

        $orderColumnIndex = isset($request['order'][0]['column']) ? intval($request['order'][0]['column']) : 0;
        $orderDir = isset($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'asc';

        $usuario_id = in_array($_SESSION['usuario_rol'], ['admin', 'superadmin']) ? null : $_SESSION['usuario_id'];

        $empresaModel = new Empresa();

        $totalRecords = $empresaModel->count($usuario_id);
        $totalFiltered = $empresaModel->contarFiltroDataTables($usuario_id, $searchValue);

        $empresas = $empresaModel->obtenerFiltroDataTables($usuario_id, $start, $length, $searchValue, $orderColumnIndex, $orderDir);

        $empresaIds = array_map(function ($e) {
            return (int)($e->id ?? 0); }, $empresas);
        $estadosTrazabilidad = [];
        if (!empty($empresaIds)) {
            $trazabilidadModel = new Trazabilidad();
            $estadosTrazabilidad = $trazabilidadModel->obtenerEstadosFlujoEmpresas($empresaIds);
        }

        $data = [];
        $labels = ['prospectado' => 'Prospectado', 'contactado' => 'Contactado', 'negociacion' => 'Negociación', 'seguimiento' => 'Seguimiento', 'ganado' => 'Ganado', 'perdido' => 'Perdido'];
        $badgeMap = ['prospectado' => 'info', 'contactado' => 'warning', 'negociacion' => 'primary', 'seguimiento' => 'secondary', 'ganado' => 'success', 'perdido' => 'danger'];

        foreach ($empresas as $e) {
            $etapa = strtolower($e->etapa_venta ?? 'prospectado');
            $estadoFlujo = $estadosTrazabilidad[(int)$e->id] ?? [
                'tiene_estudio_necesidades' => false,
                'tiene_oferta_servicios' => false,
                'tiene_seguimiento_oferta' => false,
            ];

            $contactoEfectivo = ($etapa === 'contactado' && strtoupper(trim((string)($e->aplica ?? ''))) === 'SI');
            $tieneOferta = !empty($estadoFlujo['tiene_oferta_servicios']);
            $tieneEstudio = !empty($estadoFlujo['tiene_estudio_necesidades']);
            $tieneSeguimiento = !empty($estadoFlujo['tiene_seguimiento_oferta']);

            $mostrarBadgesActividad = !in_array($etapa, ['ganado', 'perdido']);

            $badgeHtm = '<span class="badge badge-pill badge-' . ($badgeMap[$etapa] ?? 'secondary') . '">' . ($labels[$etapa] ?? ucfirst($etapa)) . '</span>';

            if ($mostrarBadgesActividad && ($contactoEfectivo || $tieneEstudio || $tieneOferta || $tieneSeguimiento)) {
                $badgeHtm .= '<div class="mt-1">';
                if ($contactoEfectivo)
                    $badgeHtm .= '<span class="badge badge-success mr-1 mb-1 d-inline-block">Contacto interesado</span>';
                if ($tieneSeguimiento) {
                    $badgeHtm .= '<span class="badge badge-info bg-info text-white mr-1 mb-1 d-inline-block">Seguimiento de la oferta</span>';
                }
                elseif ($tieneOferta) {
                    $badgeHtm .= '<span class="badge badge-primary mr-1 mb-1 d-inline-block">Oferta de servicios</span>';
                }
                elseif ($tieneEstudio) {
                    $badgeHtm .= '<span class="badge badge-primary mr-1 mb-1 d-inline-block">Estudio de necesidades</span>';
                }
                $badgeHtm .= '</div>';
            }

            $btnContactos = url('contacto/index', ['empresa_id' => $e->id]);
            $btnTrazabilidad = url('trazabilidad/index', ['empresa_id' => $e->id]);
            $btnEditar = url('empresa/editar', ['id' => $e->id]);
            $btnEliminar = url('empresa/eliminar', ['id' => $e->id]);
            $nameEscaped = htmlspecialchars($e->razon_social, ENT_QUOTES, 'UTF-8');

            $accionesHtm = '
            <div class="btn-group btn-group-sm" role="group">
                <a href="' . $btnContactos . '" class="btn btn-sm btn-outline-info" title="Contactos">
                    <i class="fas fa-address-book fa-sm"></i>
                </a>
                <a href="' . $btnTrazabilidad . '" class="btn btn-sm btn-outline-success" title="Trazabilidad">
                    <i class="fas fa-eye fa-sm"></i>
                </a>
                <a href="' . $btnEditar . '" class="btn btn-sm btn-outline-primary" title="Editar">
                    <i class="fas fa-edit fa-sm"></i>
                </a>
                <a href="#" class="btn btn-sm btn-outline-danger" title="Eliminar"
                    onclick="return confirmarEliminacion(\'' . $btnEliminar . '\', \'¿Eliminar la empresa ' . $nameEscaped . '?\')">
                    <i class="fas fa-trash-alt fa-sm"></i>
                </a>
            </div>';

            $data[] = [
                '<span class="font-weight-bold">' . htmlspecialchars($e->razon_social ?? '') . '</span>',
                htmlspecialchars($e->dpto ?? ''),
                htmlspecialchars($e->ciudad ?? ''),
                htmlspecialchars($e->actividad_economica ?? ''),
                htmlspecialchars($e->correo_comercial ?? ''),
                $badgeHtm,
                $accionesHtm
            ];
        }

        header('Content-Type: application/json');
        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        ]);
        exit;
    }
}
