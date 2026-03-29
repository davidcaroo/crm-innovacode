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
        $filtros = [
            'fecha_inicio' => isset($_GET['fecha_inicio']) ? trim((string)$_GET['fecha_inicio']) : '',
            'fecha_fin'    => isset($_GET['fecha_fin']) ? trim((string)$_GET['fecha_fin']) : '',
            'usuario_id'   => isset($_GET['usuario_id']) ? trim((string)$_GET['usuario_id']) : ''
        ];

        $usuario_id = $_SESSION['usuario_rol'] === 'usuario' ? $_SESSION['usuario_id'] : null;
        $esAdmin = in_array($_SESSION['usuario_rol'], ['admin', 'superadmin'], true);

        // Si es admin, puede filtrar por usuario especifico en todo el tablero
        if ($esAdmin && !empty($filtros['usuario_id'])) {
            $usuario_id = (int)$filtros['usuario_id'];
        }

        $stats = [
            'ventas_mes'   => $this->reporteModel->ventasMensuales($usuario_id),
            'conversion'   => $this->reporteModel->conversionRates($usuario_id),
            'actividades'  => $this->reporteModel->resumenActividades($usuario_id),
            'ranking'      => ($_SESSION['usuario_rol'] !== 'usuario') ? $this->reporteModel->rankingVendedores() : []
        ];

        $reporteGlobal = $esAdmin
            ? $this->reporteModel->resumenGlobalComercialUsuarios($filtros)
            : [];

        // Obtener lista completa de usuarios para el select del filtro
        $listaUsuarios = [];
        if ($esAdmin) {
            require_once __DIR__ . '/../models/Usuario.php';
            $usrModel = new Usuario();
            $listaUsuarios = $usrModel->todos();
        }

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
            'dataVentas'   => $dataVentas,
            'reporteGlobal' => $reporteGlobal,
            'filtrosGlobal' => $filtros,
            'esAdminGlobal' => $esAdmin,
            'listaUsuarios' => $listaUsuarios
        ]);
    }

    public function exportarGlobalExcel()
    {
        $this->requiereAdminGlobal();

        if (!class_exists('ZipArchive')) {
            http_response_code(500);
            exit('El servidor no tiene habilitada la extension ZipArchive para generar XLSX.');
        }

        $filtros = [
            'fecha_inicio' => isset($_GET['fecha_inicio']) ? trim((string)$_GET['fecha_inicio']) : '',
            'fecha_fin'    => isset($_GET['fecha_fin']) ? trim((string)$_GET['fecha_fin']) : '',
            'usuario_id'   => isset($_GET['usuario_id']) ? trim((string)$_GET['usuario_id']) : ''
        ];

        $resumen = $this->reporteModel->resumenGlobalComercialUsuarios($filtros);

        $detallesPorUsuario = [];
        $actividadesPorUsuario = [];
        foreach ($resumen as $fila) {
            $detallesPorUsuario[(int)$fila->usuario_id] = $this->reporteModel->detalleGlobalComercialPorUsuario($fila->usuario_id, $filtros);
            $actividadesPorUsuario[(int)$fila->usuario_id] = $this->reporteModel->detalleActividadesPorUsuario($fila->usuario_id, $filtros);
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx_rep_');
        if ($tmpFile === false) {
            http_response_code(500);
            exit('No se pudo crear archivo temporal para exportacion XLSX.');
        }

        $ok = $this->buildXlsxReportFile($tmpFile, $resumen, $detallesPorUsuario, $actividadesPorUsuario);
        if (!$ok) {
            @unlink($tmpFile);
            http_response_code(500);
            exit('No se pudo generar el archivo XLSX.');
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $downloadName = 'reporte_global_comercial_' . date('Ymd_His') . '.xlsx';
        if (!empty($filtros['usuario_id'])) {
            $downloadName = 'reporte_individual_' . $filtros['usuario_id'] . '_' . date('Ymd_His') . '.xlsx';
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        header('Content-Length: ' . filesize($tmpFile));
        header('Pragma: no-cache');
        header('Expires: 0');

        readfile($tmpFile);
        @unlink($tmpFile);
        exit;
    }

    private function requiereAdminGlobal()
    {
        if (!in_array($_SESSION['usuario_rol'], ['admin', 'superadmin'], true)) {
            http_response_code(403);
            exit('Acceso denegado');
        }
    }

    private function buildXlsxReportFile($filePath, $resumen, $detallesPorUsuario, $actividadesPorUsuario)
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        $sheetFiles = [];
        $sheetNames = [];
        $sheetXmls = [];

        $resumenHeaders = [
            'Usuario',
            'Email',
            'Rol',
            'Gestiones realizadas',
            'Perdidas',
            'Negociacion con oferta',
            'Contactado total',
            'Contactado con estudio',
            'Prospectado'
        ];

        $resumenRows = [];
        foreach ($resumen as $r) {
            $resumenRows[] = [
                $r->usuario,
                $r->email,
                $r->rol,
                (int)$r->gestiones_realizadas,
                (int)$r->perdidas,
                (int)$r->negociacion_con_oferta,
                (int)$r->contactado_total,
                (int)$r->contactado_con_estudio,
                (int)$r->prospectado
            ];
        }

        $sheetNames[] = 'Resumen Global';
        $sheetFiles[] = 'sheet1.xml';
        $sheetXmls[] = $this->worksheetXml($resumenHeaders, $resumenRows, [4, 5, 6, 7, 8, 9]);

        $usedNames = ['Resumen Global' => true];
        $sheetIndex = 2;

        foreach ($resumen as $r) {
            $usuarioId = (int)$r->usuario_id;
            $detalle = isset($detallesPorUsuario[$usuarioId]) ? $detallesPorUsuario[$usuarioId] : [];

            $detalleHeaders = [
                'Empresa',
                'Departamento',
                'Ciudad',
                'Correo',
                'Aplica',
                'Etapa',
                'Estudio necesidades',
                'Oferta servicios',
                'Fecha creacion'
            ];

            $detalleRows = [];
            foreach ($detalle as $d) {
                $detalleRows[] = [
                    $d->razon_social,
                    $d->dpto,
                    $d->ciudad,
                    $d->correo_comercial,
                    $d->aplica,
                    $d->etapa_venta,
                    ((int)$d->tiene_estudio_necesidades === 1) ? 'SI' : 'NO',
                    ((int)$d->tiene_oferta_servicios === 1) ? 'SI' : 'NO',
                    $d->creado_en
                ];
            }

            $baseName = $this->resolveUserSheetBaseName($r);
            $sheetName = $this->nextUniqueSheetName($baseName, $usedNames);

            $sheetNames[] = $sheetName;
            $sheetFiles[] = 'sheet' . $sheetIndex . '.xml';
            $sheetXmls[] = $this->worksheetXml($detalleHeaders, $detalleRows, []);
            $sheetIndex++;

            // Hoja 2: Actividades del Usuario
            $actividades = isset($actividadesPorUsuario[$usuarioId]) ? $actividadesPorUsuario[$usuarioId] : [];
            $actHeaders = [
                'Empresa',
                'Tipo Actividad',
                'Etapa durante Actividad',
                'Observaciones',
                'Fecha'
            ];
            $actRows = [];
            foreach ($actividades as $act) {
                $tipoRaw = strtolower((string)($act->tipo_actividad ?? ''));
                $tipoLabelMap = [
                    'llamada' => 'Llamada al cliente',
                    'correo' => 'Envío de correo',
                    'reunion' => 'Reunión acordada',
                    'visita' => 'Visita presencial',
                    'estudio_necesidades' => 'Generación de Estudio de Necesidades',
                    'oferta_servicio' => 'Envío de Oferta de Servicios',
                    'seguimiento_oferta' => 'Seguimiento de la Oferta enviada',
                    'seguimiento' => 'Seguimiento de la Oferta',
                    'nota' => 'Nota interna',
                ];
                $tipoLabel = $tipoLabelMap[$tipoRaw] ?? ucwords(str_replace('_', ' ', $tipoRaw));

                $actRows[] = [
                    $act->empresa,
                    $tipoLabel,
                    ucfirst(strtolower($act->etapa_en_momento_actividad ?? '')),
                    $act->observaciones,
                    $act->fecha_actividad
                ];
            }

            if (function_exists('mb_substr')) {
                $actBaseName = mb_substr($baseName, 0, 24, 'UTF-8') . ' Acts';
            } else {
                $actBaseName = substr($baseName, 0, 24) . ' Acts';
            }
            $actSheetName = $this->nextUniqueSheetName($actBaseName, $usedNames);

            $sheetNames[] = $actSheetName;
            $sheetFiles[] = 'sheet' . $sheetIndex . '.xml';
            $sheetXmls[] = $this->worksheetXml($actHeaders, $actRows, []);
            $sheetIndex++;
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml(count($sheetFiles)));
        $zip->addFromString('_rels/.rels', $this->rootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml($sheetNames));
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml(count($sheetFiles)));
        $zip->addFromString('xl/styles.xml', $this->stylesXml());

        foreach ($sheetFiles as $i => $fileName) {
            $zip->addFromString('xl/worksheets/' . $fileName, $sheetXmls[$i]);
        }

        $zip->close();
        return true;
    }

    private function contentTypesXml($sheetCount)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">';
        $xml .= '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>';
        $xml .= '<Default Extension="xml" ContentType="application/xml"/>';
        $xml .= '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>';
        $xml .= '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>';
        for ($i = 1; $i <= $sheetCount; $i++) {
            $xml .= '<Override PartName="/xl/worksheets/sheet' . $i . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }
        $xml .= '</Types>';
        return $xml;
    }

    private function rootRelsXml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function workbookXml($sheetNames)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';
        $xml .= '<sheets>';

        foreach ($sheetNames as $i => $name) {
            $sheetId = $i + 1;
            $xml .= '<sheet name="' . $this->xmlEscAttr($name) . '" sheetId="' . $sheetId . '" r:id="rId' . $sheetId . '"/>';
        }

        $xml .= '</sheets></workbook>';
        return $xml;
    }

    private function workbookRelsXml($sheetCount)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';

        for ($i = 1; $i <= $sheetCount; $i++) {
            $xml .= '<Relationship Id="rId' . $i . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $i . '.xml"/>';
        }

        $xml .= '<Relationship Id="rId' . ($sheetCount + 1) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';
        $xml .= '</Relationships>';
        return $xml;
    }

    private function stylesXml()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        $xml .= '<fonts count="2">';
        $xml .= '<font><sz val="11"/><name val="Calibri"/></font>';
        $xml .= '<font><b/><sz val="11"/><name val="Calibri"/></font>';
        $xml .= '</fonts>';
        $xml .= '<fills count="1"><fill><patternFill patternType="none"/></fill></fills>';
        $xml .= '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>';
        $xml .= '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>';
        $xml .= '<cellXfs count="2">';
        $xml .= '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>';
        $xml .= '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>';
        $xml .= '</cellXfs>';
        $xml .= '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>';
        $xml .= '</styleSheet>';
        return $xml;
    }

    private function worksheetXml($headers, $rows, $numericColumns)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        $xml .= '<sheetData>';

        $xml .= '<row r="1">';
        foreach ($headers as $col => $header) {
            $cellRef = $this->colLetter($col + 1) . '1';
            $xml .= '<c r="' . $cellRef . '" t="inlineStr" s="1"><is><t>' . $this->xmlEscText($header) . '</t></is></c>';
        }
        $xml .= '</row>';

        foreach ($rows as $rowIndex => $row) {
            $excelRow = $rowIndex + 2;
            $xml .= '<row r="' . $excelRow . '">';
            foreach ($row as $col => $value) {
                $cellRef = $this->colLetter($col + 1) . $excelRow;
                $col1 = $col + 1;
                if (in_array($col1, $numericColumns, true) && is_numeric($value)) {
                    $xml .= '<c r="' . $cellRef . '"><v>' . (0 + $value) . '</v></c>';
                } else {
                    $txt = $this->xmlEscText((string)$value);
                    $xml .= '<c r="' . $cellRef . '" t="inlineStr"><is><t xml:space="preserve">' . $txt . '</t></is></c>';
                }
            }
            $xml .= '</row>';
        }

        $xml .= '</sheetData></worksheet>';
        return $xml;
    }

    private function colLetter($index)
    {
        $letters = '';
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letters = chr(65 + $mod) . $letters;
            $index = (int)(($index - $mod) / 26);
        }
        return $letters;
    }

    private function xmlEscText($value)
    {
        $clean = $this->cleanXmlString((string)$value);
        return htmlspecialchars($clean, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function xmlEscAttr($value)
    {
        return $this->xmlEscText($value);
    }

    private function cleanXmlString($value)
    {
        if ($value === '') {
            return '';
        }

        if (function_exists('mb_check_encoding') && !mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }

        $value = str_replace("\r\n", "\n", $value);
        return preg_replace('/[^\x09\x0A\x0D\x20-\x{D7FF}\x{E000}-\x{FFFD}]/u', '', $value);
    }

    private function sheetNameSafe($name)
    {
        $clean = preg_replace('/[\\\/?*\[\]:]/', '_', (string)$name);
        $clean = trim($clean);
        if ($clean === '') {
            $clean = 'Hoja';
        }

        if (function_exists('mb_substr')) {
            return mb_substr($clean, 0, 31, 'UTF-8');
        }

        return substr($clean, 0, 31);
    }

    private function nextUniqueSheetName($baseName, &$used)
    {
        $name = $baseName;
        $i = 2;

        while (isset($used[$name])) {
            $suffix = ' (' . $i . ')';
            $maxLen = 31 - strlen($suffix);

            if (function_exists('mb_substr')) {
                $prefix = mb_substr($baseName, 0, $maxLen, 'UTF-8');
            } else {
                $prefix = substr($baseName, 0, $maxLen);
            }

            $name = $prefix . $suffix;
            $i++;
        }

        $used[$name] = true;
        return $name;
    }

    private function resolveUserSheetBaseName($row)
    {
        $nombre = '';
        if (isset($row->usuario)) {
            $nombre = trim((string)$row->usuario);
        }

        if ($nombre === '' && isset($row->nombre)) {
            $nombre = trim((string)$row->nombre);
        }

        if ($nombre === '' && isset($row->email)) {
            $email = trim((string)$row->email);
            if ($email !== '') {
                $atPos = strpos($email, '@');
                $nombre = ($atPos !== false) ? substr($email, 0, $atPos) : $email;
            }
        }

        if ($nombre === '' && isset($row->usuario_id)) {
            $nombre = 'Usuario_' . (int)$row->usuario_id;
        }

        return $this->sheetNameSafe($nombre);
    }
}
