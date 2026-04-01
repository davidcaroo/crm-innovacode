<?php
require_once __DIR__ . '/../models/Trazabilidad.php';
require_once __DIR__ . '/../models/Empresa.php';
require_once __DIR__ . '/../models/RecordatorioEmail.php';
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

    public function recordatorios()
    {
        $recordatorioModel = new RecordatorioEmail();
        $esAdmin = in_array($_SESSION['usuario_rol'], ['admin', 'superadmin'], true);
        $usuario_id = $esAdmin ? null : (int) $_SESSION['usuario_id'];

        $filtros = [
            'estado' => $this->get('estado') ?: '',
            'tipo_recordatorio' => $this->get('tipo_recordatorio') ?: '',
            'empresa_id' => $this->get('empresa_id') ?: '',
        ];

        $recordatorios = $recordatorioModel->obtenerListado($filtros, $usuario_id);

        $this->view('trazabilidad/recordatorios', [
            'recordatorios' => $recordatorios,
            'filtros' => $filtros,
            'esAdmin' => $esAdmin,
        ]);
    }

    public function registrar()
    {
        $empresa_id = $this->get('empresa_id') ?? $this->post('empresa_id');
        $empresa = $this->validarPropiedadEmpresa($empresa_id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipoActividadRaw = trim((string)$this->post('tipo_actividad'));
            $tipoActividadNorm = strtolower($tipoActividadRaw);
            $tipoActividadMap = [
                'llamada' => 'llamada',
                'correo' => 'correo',
                'reunion' => 'reunion',
                'reunión' => 'reunion',
                'visita' => 'visita',
                'nota' => 'nota',
                'estudio_necesidades' => 'Estudio de necesidades',
                'estudio de necesidades' => 'Estudio de necesidades',
                'oferta_servicio' => 'Oferta de servicios',
                'oferta de servicio' => 'Oferta de servicios',
                'oferta de servicios' => 'Oferta de servicios',
                'seguimiento' => 'Seguimiento de la oferta',
                'seguimiento_oferta' => 'Seguimiento de la oferta',
                'seguimiento de oferta' => 'Seguimiento de la oferta',
                'seguimiento de la oferta' => 'Seguimiento de la oferta',
            ];
            $tipoActividad = $tipoActividadMap[$tipoActividadNorm] ?? 'nota';

            $nuevaEtapa = $this->post('etapa_venta');

            if ($tipoActividad === 'Oferta de servicios') {
                // Regla de negocio: oferta enviada mueve la empresa a negociación.
                $nuevaEtapa = 'negociacion';
            } elseif ($tipoActividad === 'Seguimiento de la oferta') {
                $nuevaEtapa = 'seguimiento';
            }

            $data = [
                'empresa_id'     => $this->post('empresa_id'),
                'usuario_id'     => $_SESSION['usuario_id'],
                'etapa_venta'    => $nuevaEtapa,
                'tipo_actividad' => $tipoActividad,
                'tipo_actividad_key' => $tipoActividadNorm,
                'observaciones'  => $this->post('observaciones'),
            ];
            $trazabilidadModel = new Trazabilidad();
            $trazabilidadId = $trazabilidadModel->registrar($data);

            if ($trazabilidadId) {
                $this->programarRecordatorios($data, $empresa, (int) $trazabilidadId);
            }

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

    private function programarRecordatorios(array $data, $empresa, int $trazabilidadId): void
    {
        $recordatorioModel = new RecordatorioEmail();
            $tipoActividad = strtolower((string)($data['tipo_actividad_key'] ?? $data['tipo_actividad'] ?? ''));
        $observaciones = trim((string)($data['observaciones'] ?? ''));
        $empresaNombre = (string)($empresa->razon_social ?? 'la empresa');

        if ($tipoActividad === 'reunion') {
            $fechaProgramada = date('Y-m-d H:i:s', strtotime('+1 day'));
            $recordatorioModel->crear([
                'empresa_id' => (int) $data['empresa_id'],
                'usuario_id' => (int) $data['usuario_id'],
                'tipo_recordatorio' => 'reunion',
                'asunto' => 'Recordatorio de reunión pendiente - ' . $empresaNombre,
                'mensaje_html' => '<p>Tienes una reunión pendiente con <strong>' . htmlspecialchars($empresaNombre) . '</strong>.</p><p><strong>Detalles:</strong><br>' . nl2br(htmlspecialchars($observaciones ?: 'Sin observaciones')) . '</p>',
                'fecha_programada' => $fechaProgramada,
                'datos_json' => json_encode([
                    'trazabilidad_id' => $trazabilidadId,
                    'tipo_actividad' => 'reunion',
                    'observaciones' => $observaciones,
                    'fecha_origen' => date('Y-m-d H:i:s'),
                ], JSON_UNESCAPED_UNICODE),
            ]);
        }

        if ($tipoActividad === 'oferta_servicio') {
            $recordatorioModel->cancelarPendientesPorEmpresaYTipo((int) $data['empresa_id'], ['seguimiento_oferta']);

            $fechaProgramada = date('Y-m-d H:i:s', strtotime('+2 days'));
            $recordatorioModel->crear([
                'empresa_id' => (int) $data['empresa_id'],
                'usuario_id' => (int) $data['usuario_id'],
                'tipo_recordatorio' => 'seguimiento_oferta',
                'asunto' => 'Seguimiento pendiente de oferta - ' . $empresaNombre,
                'mensaje_html' => '<p>La empresa <strong>' . htmlspecialchars($empresaNombre) . '</strong> se encuentra en <strong>Oferta de servicios</strong>.</p><p>No olvides realizar el seguimiento correspondiente.</p><p><strong>Detalles:</strong><br>' . nl2br(htmlspecialchars($observaciones ?: 'Sin observaciones')) . '</p>',
                'fecha_programada' => $fechaProgramada,
                'datos_json' => json_encode([
                    'trazabilidad_id' => $trazabilidadId,
                    'tipo_actividad' => 'oferta_servicio',
                    'observaciones' => $observaciones,
                    'fecha_origen' => date('Y-m-d H:i:s'),
                ], JSON_UNESCAPED_UNICODE),
            ]);
        }

        if ($tipoActividad === 'seguimiento_oferta') {
            $recordatorioModel->cancelarPendientesPorEmpresaYTipo((int) $data['empresa_id'], ['seguimiento_oferta']);
        }
    }

    public function exportar()
    {
        // Configuración de tiempo y memoria para datasets grandes
        set_time_limit(300);
        ini_set('memory_limit', '256M');

        // Capturar filtros
        $filtros = [];

        // Aplicar filtro de permisos según rol
        if (!in_array($_SESSION['usuario_rol'], ['admin', 'superadmin'])) {
            $filtros['usuario_id'] = $_SESSION['usuario_id'];
        }

        // Filtros opcionales
        if ($empresa_id = $this->get('empresa_id')) {
            // Validar propiedad de la empresa
            $this->validarPropiedadEmpresa($empresa_id);
            $filtros['empresa_id'] = $empresa_id;
        }

        if ($fecha_inicio = $this->get('fecha_inicio')) {
            $filtros['fecha_inicio'] = $fecha_inicio;
        }

        if ($fecha_fin = $this->get('fecha_fin')) {
            $filtros['fecha_fin'] = $fecha_fin;
        }

        if ($tipo_actividad = $this->get('tipo_actividad')) {
            $filtros['tipo_actividad'] = $tipo_actividad;
        }

        // Obtener datos
        $trazabilidadModel = new Trazabilidad();
        $datos = $trazabilidadModel->historialParaExportar($filtros);

        // Generar nombre de archivo
        $timestamp = date('Ymd_His');
        if (!empty($filtros['empresa_id'])) {
            $filename = "trazabilidad_empresa-{$filtros['empresa_id']}_{$timestamp}.csv";
        } else {
            $filename = "trazabilidad_global_{$timestamp}.csv";
        }

        // Configurar headers para descarga CSV
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        // Abrir output stream
        $output = fopen('php://output', 'w');

        // Agregar BOM UTF-8 para correcta detección en Excel
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Escribir encabezados
        $encabezados = [
            'ID',
            'Fecha',
            'Empresa ID',
            'Empresa',
            'Departamento',
            'Ciudad',
            'Actividad Económica',
            'Correo Comercial',
            'Etapa Actual Empresa',
            'Usuario ID',
            'Usuario',
            'Rol Usuario',
            'Tipo Actividad',
            'Etapa en Actividad',
            'Observaciones',
            'Días Transcurridos'
        ];
        fputcsv($output, $encabezados, ',');

        // Escribir datos
        foreach ($datos as $fila) {
            $tipoRaw = strtolower((string)($fila->tipo_actividad ?? ''));
            $tipoLabelMap = [
                'llamada' => 'Llamada',
                'correo' => 'Correo',
                'reunion' => 'Reunion',
                'visita' => 'Visita',
                'estudio_necesidades' => 'Estudio de Necesidades',
                'oferta_servicio' => 'Oferta de Servicio',
                'seguimiento_oferta' => 'Seguimiento de la Oferta',
                'seguimiento de la oferta' => 'Seguimiento de la Oferta',
                'seguimiento' => 'Seguimiento de la Oferta',
                'nota' => 'Nota',
            ];
            $tipoLabel = $tipoLabelMap[$tipoRaw] ?? ucwords(str_replace('_', ' ', $tipoRaw));

            $row = [
                $fila->id,
                $fila->fecha_formateada,
                $fila->empresa_id,
                $fila->empresa,
                $fila->dpto ?? '',
                $fila->ciudad ?? '',
                $fila->actividad_economica ?? '',
                $fila->correo_comercial ?? '',
                ucfirst($fila->etapa_actual_empresa ?? ''),
                $fila->usuario_id,
                $fila->usuario,
                ucfirst($fila->rol_usuario ?? ''),
                $tipoLabel,
                ucfirst($fila->etapa_en_actividad ?? ''),
                $fila->observaciones ?? '',
                $fila->dias_transcurridos
            ];
            fputcsv($output, $row, ',');
        }

        fclose($output);
        exit;
    }
}
