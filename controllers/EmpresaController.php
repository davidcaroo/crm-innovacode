<?php
require_once __DIR__ . '/../models/Empresa.php';
require_once __DIR__ . '/../models/Configuracion.php';
require_once __DIR__ . '/../models/Mailer.php';
require_once __DIR__ . '/../models/Notificacion.php';
require_once __DIR__ . '/BaseController.php';

class EmpresaController extends BaseController
{
    public function index()
    {
        $empresaModel = new Empresa();
        $buscar = $this->get('buscar');

        // Si hay término de búsqueda, buscar; si no, obtener todas
        if (!empty($buscar)) {
            $usuario_id = in_array($_SESSION['usuario_rol'], ['admin', 'superadmin']) ? null : $_SESSION['usuario_id'];
            $empresas = $empresaModel->buscar($buscar, $usuario_id);
        } else {
            if (in_array($_SESSION['usuario_rol'], ['admin', 'superadmin'])) {
                $empresas = $empresaModel->todasAdmin();
            } else {
                $empresas = $empresaModel->todasPorUsuario($_SESSION['usuario_id']);
            }
        }

        $this->view('empresas/index', ['empresas' => $empresas, 'buscar' => $buscar ?? '']);
    }

    public function crear()
    {
        $this->view('empresas/crear');
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'razon_social' => $this->post('razon_social'),
                'dpto' => $this->post('dpto'),
                'ciudad' => $this->post('ciudad'),
                'actividad_economica' => $this->post('actividad_economica'),
                'correo_comercial' => $this->post('correo_comercial'),
                'aplica' => $this->post('aplica'),
                'etapa_venta' => $this->post('etapa_venta'),
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
            $nuevaEtapa = $this->post('etapa_venta');

            $data = [
                'razon_social' => $this->post('razon_social'),
                'dpto' => $this->post('dpto'),
                'ciudad' => $this->post('ciudad'),
                'actividad_economica' => $this->post('actividad_economica'),
                'correo_comercial' => $this->post('correo_comercial'),
                'aplica' => $this->post('aplica'),
                'etapa_venta' => $nuevaEtapa,
                'observaciones' => $this->post('observaciones')
            ];

            $empresaModel->actualizar($id, $data, $usuario_id);

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
            } elseif ($nuevaEtapa !== $etapaAnterior) {
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
        } catch (Exception $e) {
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
        $etapas = ['prospectado' => [], 'contactado' => [], 'negociacion' => [], 'ganado' => [], 'perdido' => []];
        foreach ($todasEmpresas as $emp) {
            $etapa = $emp->etapa_venta ?? 'prospectado';
            if (!isset($etapas[$etapa])) $etapa = 'prospectado';
            $etapas[$etapa][] = $emp;
        }

        $this->view('empresas/pipeline', ['etapas' => $etapas]);
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
                    if (empty($data[0])) continue;

                    // Mapeo detallado basado en la estructura del usuario (A-K):
                    // A(0): Razon Social, B(1): Dpto, C(2): Ciudad, D(3): Actividad, E(4): Correo, F(5): Aplica
                    // G(6): Fuente Tel, H(7): Contacto, I(8): Tel, J(9): Etapa, K(10): Obs

                    $extraObs = "";
                    if (!empty($data[6])) $extraObs .= "Fuente Tel: " . $data[6] . ". ";
                    if (!empty($data[7])) $extraObs .= "Contacto: " . $data[7] . ". ";
                    if (!empty($data[8])) $extraObs .= "Tel: " . $data[8] . ". ";

                    $observaciones = ($data[10] ?? '') . ($extraObs ? "\n--- Info Importada ---\n" . $extraObs : "");

                    $filas[] = [
                        'razon_social'        => $data[0] ?? '',
                        'dpto'               => $data[1] ?? '',
                        'ciudad'             => $data[2] ?? '',
                        'actividad_economica' => $data[3] ?? '',
                        'correo_comercial'   => $data[4] ?? '',
                        'aplica'             => $data[5] ?? 'Si',
                        'etapa_venta'        => $data[9] ?? 'prospectado', // Índice 9 según imagen del usuario
                        'observaciones'      => $observaciones              // Índice 10 + extras
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
                } else {
                    $this->redirect(BASE_URL . '/index.php?controller=empresa&action=importar&error=db');
                }
            } else {
                $this->redirect(BASE_URL . '/index.php?controller=empresa&action=importar&error=upload');
            }
        }
    }
}
