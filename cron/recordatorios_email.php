<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/RecordatorioEmail.php';
require_once __DIR__ . '/../models/Mailer.php';
require_once __DIR__ . '/../models/Notificacion.php';

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('Solo disponible por CLI.');
}

$recordatorioModel = new RecordatorioEmail();
$recordatorios = $recordatorioModel->obtenerPendientesVencidos(200);

foreach ($recordatorios as $recordatorio) {
    $datos = [];
    if (!empty($recordatorio->datos_json)) {
        $decoded = json_decode($recordatorio->datos_json, true);
        if (is_array($decoded)) {
            $datos = $decoded;
        }
    }

    $empresa = $recordatorio->razon_social ?? 'la empresa';
    $tipo = $recordatorio->tipo_recordatorio ?? 'recordatorio';
    $asunto = $recordatorio->asunto ?: ('Recordatorio CRM - ' . $empresa);
    $tipoLabelMap = [
        'reunion' => 'Reunión pendiente',
        'seguimiento_oferta' => 'Seguimiento de oferta',
        'oferta_servicio' => 'Oferta de servicios',
        'general' => 'Recordatorio general',
    ];

    $fechaProgramada = !empty($recordatorio->fecha_programada)
        ? date('d/m/Y H:i', strtotime($recordatorio->fecha_programada))
        : '';

    $mensaje = Mailer::plantillaRecordatorio([
        'nombre_usuario' => $recordatorio->usuario_nombre ?? 'usuario',
        'empresa' => $empresa,
        'tipo_label' => $tipoLabelMap[$tipo] ?? ucfirst(str_replace('_', ' ', $tipo)),
        'asunto' => $asunto,
        'fecha_programada' => $fechaProgramada,
        'detalle_html' => !empty($recordatorio->mensaje_html) ? $recordatorio->mensaje_html : '<p>Sin detalles adicionales.</p>',
        'cta_url' => defined('BASE_URL') ? BASE_URL . '/recordatorios' : '#',
        'accent' => '#0f766e',
    ]);

    $destinatario = $recordatorio->usuario_email ?? null;
    if (empty($destinatario)) {
        $recordatorioModel->marcarFallido((int) $recordatorio->id, 'Usuario sin correo configurado');
        continue;
    }

    try {
        $enviado = Mailer::enviarRecordatorio($destinatario, $asunto, $mensaje);
        if ($enviado) {
            $recordatorioModel->marcarEnviado((int) $recordatorio->id);
            Notificacion::crear((int) $recordatorio->usuario_id, 'recordatorio_email', $asunto, strip_tags($mensaje), '');
        } else {
            $recordatorioModel->marcarFallido((int) $recordatorio->id, 'Fallo al enviar correo SMTP');
        }
    } catch (Exception $e) {
        $recordatorioModel->marcarFallido((int) $recordatorio->id, $e->getMessage());
    }
}

echo 'Procesados: ' . count($recordatorios) . PHP_EOL;