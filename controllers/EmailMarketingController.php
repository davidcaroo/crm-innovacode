<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Empresa.php';
require_once __DIR__ . '/../models/PlantillaEmail.php';
require_once __DIR__ . '/../models/PlantillaAdjunto.php';
require_once __DIR__ . '/../models/EnvioEmail.php';
require_once __DIR__ . '/../models/Mailer.php'; // si tienes una clase Mailer para correos

class EmailMarketingController extends BaseController {

    private $empresaModel;
    private $plantillaModel;
    private $adjuntoModel;
    private $envioModel;

    public function __construct() {
        $this->empresaModel = new Empresa();
        $this->plantillaModel = new PlantillaEmail();
        $this->adjuntoModel = new PlantillaAdjunto();
        $this->envioModel = new EnvioEmail();
    }

    /**
     * Muestra la lista de empresas para seleccionar destinatarios
     */
    public function index() {
        $usuario_id = (isset($_SESSION['usuario_rol']) && ($_SESSION['usuario_rol'] === 'admin' || $_SESSION['usuario_rol'] === 'superadmin')) ? null : $_SESSION['usuario_id'];
        
        $empresas = $this->empresaModel->obtenerParaMarketing($usuario_id);
        
        $this->view('email_marketing/index', [
            'titulo' => 'Email Marketing - Enviar Oferta',
            'empresas' => $empresas
        ]);
    }

    /**
     * Redactar correo o elegir plantilla para empresas seleccionadas
     */
    public function redactar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(url('emailMarketing/index'));
        }

        $empresas_ids = $_POST['empresas_ids'] ?? [];
        if (empty($empresas_ids)) {
            $this->redirect(url('emailMarketing/index'));
        }

        $empresas = [];
        foreach ($empresas_ids as $id) {
            // Buscamos la empresa y forzamos el fetch asociativo
            $stmt = $this->empresaModel->getDb()->prepare("SELECT * FROM empresas WHERE id = ?");
            $stmt->execute([$id]);
            $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($empresa) {
                // Agregar el correo de la empresa si existe
                $empresas[] = $empresa;
            }
        }

        $plantillas = $this->plantillaModel->obtenerTodas();

        $this->view('email_marketing/redactar', [
            'title' => 'Redactar Campaña / Mensaje',
            'empresas' => $empresas,
            'plantillas' => $plantillas,
            'empresas_ids_json' => json_encode($empresas_ids)
        ]);
    }

    /**
     * Procesa el envío masivo o individual
     */
    public function enviar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(url('emailMarketing/index'));
        }

        $empresas_ids = json_decode($_POST['empresas_ids'] ?? '[]');
        $asunto = $_POST['asunto'] ?? '';
        $cuerpo_html = $_POST['cuerpo_html'] ?? '';
        $plantilla_id = $_POST['plantilla_id'] ?? null;

        if (empty($empresas_ids) || empty($asunto) || empty($cuerpo_html)) {
            $this->redirect(url('emailMarketing/index'));
        }

        $adjuntos_envio = [];
        if ($plantilla_id) {
            $plantilla = $this->plantillaModel->find($plantilla_id);
            if ($plantilla) {
                // $plantilla es un objeto segun la definicion de BaseModel::find()
                $cuerpo_html = $plantilla->cuerpo_html;
                
                // Cargar adjuntos si existen
                $adjuntos_registrados = $this->adjuntoModel->obtenerPorPlantilla($plantilla_id);
                foreach ($adjuntos_registrados as $adj) {
                    $adjuntos_envio[] = [
                        'ruta' => $adj['ruta_archivo'],
                        'nombre' => $adj['nombre_original']
                    ];
                }
            }
        }
        
        $enviados = 0;
        $fallidos = 0;

        foreach ($empresas_ids as $id) {
            // Forzar fetch asociativo para evitar errores stdClass
            $stmt = $this->empresaModel->getDb()->prepare("SELECT * FROM empresas WHERE id = ?");
            $stmt->execute([$id]);
            $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // $empresa es un arreglo segun el fetch anterior
            $correo = $empresa['correo_comercial'] ?? null;

            if ($correo) {
                // Reemplazar variables en el HTML
                $htmlPersonalizado = str_replace('{{empresa}}', $empresa['razon_social'], $cuerpo_html);
                
                try {
                    // Usar Mailer::enviarMarketing (estático) ahora con adjuntos
                    $res = Mailer::enviarMarketing($correo, $asunto, $htmlPersonalizado, $adjuntos_envio);
                    
                    if ($res) {
                        $this->envioModel->registrarEnvio($id, $asunto, count($empresas_ids) > 1 ? 'masivo' : 'individual', 'enviado', $_SESSION['usuario_id']);
                        $enviados++;
                    } else {
                        $this->envioModel->registrarEnvio($id, $asunto, count($empresas_ids) > 1 ? 'masivo' : 'individual', 'fallido', $_SESSION['usuario_id'], 'Fallo en la conexión SMTP o configuración');
                        $fallidos++;
                    }
                } catch (\Exception $e) {
                    $this->envioModel->registrarEnvio($id, $asunto, count($empresas_ids) > 1 ? 'masivo' : 'individual', 'fallido', $_SESSION['usuario_id'], $e->getMessage());
                    $fallidos++;
                }
            } else {
                $this->envioModel->registrarEnvio($id, $asunto, count($empresas_ids) > 1 ? 'masivo' : 'individual', 'fallido', $_SESSION['usuario_id'], 'Sin correo configurado');
                $fallidos++;
            }
        }

        $this->redirect(url('emailMarketing/index'));
    }

    /**
     * Gestión de plantillas
     */
    public function plantillas() {
        $plantillas = $this->plantillaModel->obtenerTodas();
        
        $this->view('email_marketing/plantillas', [
            'title' => 'Gestión de Plantillas HTML',
            'plantillas' => $plantillas
        ]);
    }

    public function crearPlantilla() {
        $this->view('email_marketing/crear_plantilla', [
            'title' => 'Crear Plantilla HTML'
        ]);
    }

    public function guardarPlantilla() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $asunto = trim($_POST['asunto'] ?? '');
            $cuerpo_html = $_POST['cuerpo_html'] ?? '';

            if (empty($nombre) || empty($asunto) || empty($cuerpo_html)) {
                $this->redirect(url('emailMarketing/crearPlantilla'));
            }

            $contentLength = isset($_SERVER['CONTENT_LENGTH']) ? (int) $_SERVER['CONTENT_LENGTH'] : 0;
            if ($contentLength > 0) {
                $maxUpload = $this->toBytes(ini_get('upload_max_filesize'));
                $maxPost = $this->toBytes(ini_get('post_max_size'));
                $limit = min($maxUpload > 0 ? $maxUpload : PHP_INT_MAX, $maxPost > 0 ? $maxPost : PHP_INT_MAX);

                if ($limit > 0 && $contentLength > $limit) {
                    $this->redirect(url('emailMarketing/crearPlantilla', ['error' => 'El contenido excede el tamaño permitido por el servidor. Reduce el HTML o los adjuntos.']));
                }
            }

            try {
                $plantillaId = $this->plantillaModel->crear([
                    'nombre' => $nombre,
                    'asunto' => $asunto,
                    'cuerpo_html' => $cuerpo_html,
                    'usuario_id' => $_SESSION['usuario_id']
                ]);

                // Procesar adjuntos
                if (isset($_FILES['adjuntos']) && !empty($_FILES['adjuntos']['name'][0])) {
                    $uploadDir = __DIR__ . '/../public/uploads/email_marketing/';
                    
                    foreach ($_FILES['adjuntos']['name'] as $key => $name) {
                        $tmp_name = $_FILES['adjuntos']['tmp_name'][$key];
                        $clean_name = time() . '_' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $name);
                        $target = $uploadDir . $clean_name;
                        
                        if (move_uploaded_file($tmp_name, $target)) {
                            $this->adjuntoModel->registrar($plantillaId, $name, $target);
                        }
                    }
                }

                $this->redirect(url('emailMarketing/plantillas'));
            } catch (\Exception $e) {
                error_log('EmailMarketing guardarPlantilla error: ' . $e->getMessage());
                $this->redirect(url('emailMarketing/crearPlantilla', ['error' => 'No se pudo guardar la plantilla.']));
            }
        }
    }

    public function obtenerPlantillaAjax() {
        if (!isset($_GET['id'])) {
            echo json_encode(['error' => 'ID no proporcionado']);
            exit;
        }

        $plantilla = $this->plantillaModel->find($_GET['id']);
        header('Content-Type: application/json');
        if ($plantilla) {
            echo json_encode($plantilla);
        } else {
            echo json_encode(['error' => 'Plantilla no encontrada']);
        }
        exit;
    }
    
    public function eliminarPlantilla() {
        $id = $this->get('id');
        if (!$id) {
            $this->redirect(url('emailMarketing/plantillas'));
        }
        
        try {
            $this->plantillaModel->delete($id);
            $this->redirect(url('emailMarketing/plantillas'));
        } catch (\Exception $e) {
            $this->redirect(url('emailMarketing/plantillas'));
        }
    }

    private function toBytes($value) {
        $value = trim((string) $value);
        if ($value === '') {
            return 0;
        }

        $unit = strtolower(substr($value, -1));
        $number = (int) $value;

        switch ($unit) {
            case 'g':
                return $number * 1024 * 1024 * 1024;
            case 'm':
                return $number * 1024 * 1024;
            case 'k':
                return $number * 1024;
            default:
                return (int) $value;
        }
    }
}
