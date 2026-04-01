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

    $detalleHtml = '';
    if (!empty($datos['observaciones'])) {
        $detalleHtml .= '<p><strong>Detalles:</strong><br>' . nl2br(htmlspecialchars($datos['observaciones'])) . '</p>';
    }
    if (!empty($datos['fecha_origen'])) {
        $detalleHtml .= '<p><strong>Registrado el:</strong> ' . htmlspecialchars($datos['fecha_origen']) . '</p>';
    }

    $mensaje = '<html><body style="font-family:Arial,sans-serif;background:#f8fafc;padding:24px;">'
        . '<div style="max-width:640px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:24px;">'
        . '<h2 style="margin:0 0 12px 0;color:#1d4ed8;">Recordatorio automático</h2>'
        . '<p>Hola <strong>' . htmlspecialchars($recordatorio->usuario_nombre ?? 'usuario') . '</strong>,</p>'
        . '<p>Este es un recordatorio sobre <strong>' . htmlspecialchars($empresa) . '</strong>.</p>'
        . '<p><strong>Tipo:</strong> ' . htmlspecialchars($tipo) . '</p>'
        . $detalleHtml
        . '<p style="margin-top:24px;color:#6b7280;font-size:13px;">Enviado automáticamente por el CRM.</p>'
        . '</div></body></html>';

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