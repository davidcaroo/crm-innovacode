<?php
// views/configuracion/index.php
// Variables: $smtp, $integraciones, $eventos, $prefsRoles, $roles, $tab
?>

<div class="page-header mb-4">
    <div>
        <h2 class="page-title"><span class="mdi mdi-cog-outline"></span> Configuración Avanzada</h2>
        <span class="page-subtitle">Gestiona SMTP, integraciones externas y preferencias de notificaciones</span>
    </div>
</div>

<?php
$okMsgs = [
    'smtp'  => '<i class="mdi mdi-check-circle mr-1"></i> Configuración SMTP guardada correctamente.',
    'int'   => '<i class="mdi mdi-check-circle mr-1"></i> Integración guardada correctamente.',
    'notif' => '<i class="mdi mdi-check-circle mr-1"></i> Preferencias de notificación actualizadas.',
];
$ok = $_GET['ok'] ?? null;
if ($ok && isset($okMsgs[$ok])):
?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" style="border-radius:10px;border:none;">
        <?= $okMsgs[$ok] ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<!-- TABS NAVEGACIÓN -->
<ul class="nav nav-pills mb-4" id="configTabs" style="background:#fff;border-radius:12px;padding:8px;box-shadow:0 1px 4px rgba(44,62,80,.06);border:1px solid #e4e8f0;gap:4px;">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'smtp' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/index.php?controller=configuracion&action=editar&tab=smtp"
           style="border-radius:8px;font-weight:600;">
            <i class="mdi mdi-email-outline mr-1"></i> Comunicaciones
            <?php if (!Configuracion::smtpConfigurado()): ?>
                <span class="badge badge-warning ml-1" style="font-size:0.65rem;">Sin configurar</span>
            <?php else: ?>
                <span class="badge badge-success ml-1" style="font-size:0.65rem;">Activo</span>
            <?php endif; ?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'integraciones' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/index.php?controller=configuracion&action=editar&tab=integraciones"
           style="border-radius:8px;font-weight:600;">
            <i class="mdi mdi-api mr-1"></i> Integraciones
            <?php
                $activasCount = count(array_filter((array)$integraciones, fn($i) => $i->estado === 'activa'));
            ?>
            <?php if ($activasCount > 0): ?>
                <span class="badge badge-primary ml-1" style="font-size:0.65rem;"><?= $activasCount ?> activa<?= $activasCount !== 1 ? 's' : '' ?></span>
            <?php endif; ?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'notificaciones' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/index.php?controller=configuracion&action=editar&tab=notificaciones"
           style="border-radius:8px;font-weight:600;">
            <i class="mdi mdi-bell-outline mr-1"></i> Notificaciones
        </a>
    </li>
</ul>

<!-- ====================================================== -->
<!-- TAB: COMUNICACIONES / SMTP                              -->
<!-- ====================================================== -->
<?php if ($tab === 'smtp'): ?>
<div class="row">
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm border-0" style="border-radius:14px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <span class="mdi mdi-email-send-outline" style="font-size:1.8rem;color:#1e40af;margin-right:12px;"></span>
                    <div>
                        <h5 class="mb-0" style="font-weight:700;color:#1e40af;">Configuración SMTP</h5>
                        <small class="text-muted">Necesario para envío de correos y alertas del sistema.</small>
                    </div>
                </div>

                <?php if (!Configuracion::smtpConfigurado()): ?>
                    <div class="alert alert-warning" style="border-radius:10px;border:none;">
                        <i class="mdi mdi-alert-outline mr-1"></i>
                        <strong>SMTP sin configurar.</strong> Las notificaciones por email no funcionarán hasta que completes este formulario.
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= BASE_URL ?>/index.php?controller=configuracion&action=guardarSmtp" autocomplete="off">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:.85rem;">Host SMTP</label>
                                <input type="text" class="form-control" name="smtp_host"
                                       value="<?= htmlspecialchars($smtp['smtp_host'] ?? '') ?>"
                                       placeholder="smtp.gmail.com">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:.85rem;">Puerto</label>
                                <input type="number" class="form-control" name="smtp_port"
                                       value="<?= htmlspecialchars($smtp['smtp_port'] ?? 587) ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold" style="font-size:.85rem;">Usuario / Email</label>
                        <input type="email" class="form-control" name="smtp_user"
                               value="<?= htmlspecialchars($smtp['smtp_user'] ?? '') ?>"
                               placeholder="tu@empresa.com">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold" style="font-size:.85rem;">Contraseña</label>
                        <input type="password" class="form-control" name="smtp_pass"
                               placeholder="<?= !empty($smtp['smtp_host']) ? '••••••••••  (dejar vacío para no cambiar)' : 'Contraseña SMTP' ?>">
                        <small class="text-muted">Se cifra con AES-256 antes de almacenarse.</small>
                    </div>

                    <hr>
                    <div class="custom-control custom-switch mb-4">
                        <input type="checkbox" class="custom-control-input" id="notif_ganado"
                               name="notificaciones_ganado" <?= ($smtp['notificaciones_ganado'] ?? false) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="notif_ganado">
                            Enviar email al cerrar una oportunidad como <strong>Ganada</strong>
                        </label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary font-weight-700 mr-2" style="border-radius:8px;font-weight:600;">
                            <i class="mdi mdi-content-save mr-1"></i> Guardar SMTP
                        </button>
                        <button type="button" id="btnProbarSmtp" class="btn btn-outline-secondary" style="border-radius:8px;font-weight:600;">
                            <i class="mdi mdi-send-check-outline mr-1"></i> Probar conexión
                        </button>
                    </div>
                    <div id="smtpTestResult" class="mt-3" style="display:none;"></div>
                </form>
            </div>
        </div>
    </div>

    <!-- Ayuda contextual -->
    <div class="col-12 col-lg-5 mt-4 mt-lg-0">
        <div class="card border-0" style="background:#f0f7ff;border-radius:14px;">
            <div class="card-body p-4">
                <h6 class="font-weight-bold mb-3" style="color:#1e40af;"><i class="mdi mdi-lightbulb-on-outline mr-1"></i> Guía de configuración</h6>
                <p style="font-size:.88rem;color:#374151;"><strong>Gmail:</strong><br>Host: <code>smtp.gmail.com</code>, Puerto: <code>587</code><br>Activar "Contraseñas de aplicación" en tu cuenta Google.</p>
                <p style="font-size:.88rem;color:#374151;"><strong>Outlook/Hotmail:</strong><br>Host: <code>smtp-mail.outlook.com</code>, Puerto: <code>587</code></p>
                <p style="font-size:.88rem;color:#374151;"><strong>SendGrid:</strong><br>Host: <code>smtp.sendgrid.net</code>, Puerto: <code>587</code><br>Usuario: <code>apikey</code>, Contraseña: tu API Key.</p>
                <hr>
                <p class="mb-0" style="font-size:.82rem;color:#64748b;"><i class="mdi mdi-shield-check-outline mr-1"></i> La contraseña se almacena cifrada con AES-256-CBC.</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ====================================================== -->
<!-- TAB: INTEGRACIONES                                       -->
<!-- ====================================================== -->
<?php if ($tab === 'integraciones'): ?>

<?php
$iconosIntegracion = [
    'whatsapp'         => ['icono' => 'mdi-whatsapp',          'color' => '#25D366', 'bg' => '#e8fdf1'],
    'google_calendar'  => ['icono' => 'mdi-calendar-check',    'color' => '#4285F4', 'bg' => '#e8f0fe'],
    'google_maps'      => ['icono' => 'mdi-map-marker',        'color' => '#EA4335', 'bg' => '#fde8e8'],
    'webhook_generico' => ['icono' => 'mdi-webhook',           'color' => '#7c3aed', 'bg' => '#f3eeff'],
    'zapier'           => ['icono' => 'mdi-lightning-bolt',    'color' => '#FF4A00', 'bg' => '#fff0eb'],
];

$camposPorSlug = [
    'whatsapp'         => [['campo' => 'numero_whatsapp', 'label' => 'Número WhatsApp', 'tipo' => 'text',     'ph' => '+1234567890'], ['campo' => 'api_key', 'label' => 'API Key (opcional)', 'tipo' => 'text', 'ph' => 'sk-...']],
    'google_calendar'  => [['campo' => 'api_key',         'label' => 'API Key de Google', 'tipo' => 'text',   'ph' => 'AIza...']],
    'google_maps'      => [['campo' => 'api_key',         'label' => 'API Key de Google Maps', 'tipo' => 'text', 'ph' => 'AIza...']],
    'webhook_generico' => [['campo' => 'url_webhook',     'label' => 'URL del Webhook', 'tipo' => 'url',      'ph' => 'https://...'], ['campo' => 'secret', 'label' => 'Secret (opcional)', 'tipo' => 'text', 'ph' => '...']],
    'zapier'           => [['campo' => 'webhook_url',     'label' => 'Zapier Webhook URL', 'tipo' => 'url',   'ph' => 'https://hooks.zapier.com/...']],
];
?>

<div class="row">
    <?php foreach ($integraciones as $integ):
        $meta     = $iconosIntegracion[$integ->slug] ?? ['icono' => 'mdi-api', 'color' => '#1e40af', 'bg' => '#e8f0fe'];
        $campos_c = $camposPorSlug[$integ->slug] ?? [];
        $vals     = !empty($integ->campos) ? (json_decode(Configuracion::desencriptar($integ->campos), true) ?? []) : [];
    ?>
    <div class="col-12 col-md-6 col-xl-4 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius:14px;border-top:3px solid <?= $meta['color'] ?>!important;">
            <div class="card-body p-4">
                <!-- Cabecera -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <span class="mdi <?= $meta['icono'] ?>" style="font-size:2rem;color:<?= $meta['color'] ?>;margin-right:10px;"></span>
                        <div>
                            <div style="font-weight:700;font-size:1rem;"><?= htmlspecialchars($integ->nombre) ?></div>
                            <small class="text-muted" style="font-size:.75rem;"><?= htmlspecialchars($integ->descripcion ?? '') ?></small>
                        </div>
                    </div>
                    <!-- Badge estado -->
                    <?php if ($integ->estado === 'activa'): ?>
                        <span class="badge badge-success" style="border-radius:8px;padding:5px 10px;font-size:.75rem;">Activa</span>
                    <?php elseif ($integ->estado === 'error'): ?>
                        <span class="badge badge-danger" style="border-radius:8px;padding:5px 10px;font-size:.75rem;" title="<?= htmlspecialchars($integ->error_msg ?? '') ?>">Error</span>
                    <?php else: ?>
                        <span class="badge badge-secondary" style="border-radius:8px;padding:5px 10px;font-size:.75rem;">Inactiva</span>
                    <?php endif; ?>
                </div>

                <!-- Formulario de campos -->
                <form method="post" action="<?= BASE_URL ?>/index.php?controller=configuracion&action=guardarIntegracion">
                    <input type="hidden" name="slug" value="<?= htmlspecialchars($integ->slug) ?>">

                    <?php foreach ($campos_c as $c): ?>
                        <div class="form-group mb-2">
                            <label class="font-weight-bold mb-1" style="font-size:.78rem;color:#64748b;text-transform:uppercase;letter-spacing:.5px;"><?= htmlspecialchars($c['label']) ?></label>
                            <input type="<?= $c['tipo'] ?>" class="form-control form-control-sm"
                                   name="campo_<?= $c['campo'] ?>"
                                   value="<?= htmlspecialchars($vals[$c['campo']] ?? '') ?>"
                                   placeholder="<?= $c['ph'] ?>">
                        </div>
                    <?php endforeach; ?>

                    <!-- Estado toggle -->
                    <div class="custom-control custom-switch mb-3 mt-2">
                        <input type="checkbox" class="custom-control-input" id="est_<?= $integ->slug ?>"
                               name="estado" value="activa" <?= $integ->estado === 'activa' ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="est_<?= $integ->slug ?>" style="font-size:.88rem;">
                            Activar integración
                        </label>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary btn-block" style="border-radius:8px;font-weight:600;">
                        <i class="mdi mdi-content-save mr-1"></i> Guardar
                    </button>
                </form>

                <?php if ($integ->estado === 'error' && !empty($integ->error_msg)): ?>
                    <div class="alert alert-danger mt-2 mb-0 p-2" style="border-radius:8px;font-size:.8rem;">
                        <i class="mdi mdi-alert mr-1"></i><?= htmlspecialchars($integ->error_msg) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="card border-0 shadow-sm mt-2" style="border-radius:12px;background:#f8fafc;">
    <div class="card-body p-3">
        <small class="text-muted"><i class="mdi mdi-shield-lock-outline mr-1"></i> Todos los campos sensibles (API Keys) se cifran con AES-256-CBC antes de guardarse en la base de datos.</small>
    </div>
</div>

<?php endif; ?>

<!-- ====================================================== -->
<!-- TAB: NOTIFICACIONES                                      -->
<!-- ====================================================== -->
<?php if ($tab === 'notificaciones'): ?>

<?php
$etiquetasRoles = ['superadmin' => 'Superadmin', 'admin' => 'Admin', 'usuario' => 'Vendedor'];
?>

<div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-1">
            <span class="mdi mdi-bell-cog-outline" style="font-size:1.6rem;color:#1e40af;margin-right:10px;"></span>
            <div>
                <h5 class="mb-0" style="font-weight:700;color:#1e40af;">Reglas de Notificación por Rol</h5>
                <small class="text-muted">Define qué eventos notifican a cada rol y por qué canal.</small>
            </div>
        </div>
        <?php if (!Configuracion::smtpConfigurado()): ?>
            <div class="alert alert-warning mt-3 mb-0" style="border-radius:10px;border:none;font-size:.88rem;">
                <i class="mdi mdi-email-alert-outline mr-1"></i>
                <strong>Advertencia:</strong> El canal Email no funcionará hasta que configures el SMTP en la pestaña <a href="?controller=configuracion&action=editar&tab=smtp">Comunicaciones</a>.
            </div>
        <?php endif; ?>
    </div>
</div>

<form method="post" action="<?= BASE_URL ?>/index.php?controller=configuracion&action=guardarNotificaciones">
    <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
        <div class="table-responsive">
            <table class="table mb-0" style="font-size:.9rem;">
                <thead style="background:#f8fafc;">
                    <tr>
                        <th style="padding:16px 20px;font-weight:700;color:#374151;">Evento</th>
                        <?php foreach ($roles as $rol): ?>
                            <th colspan="2" class="text-center" style="padding:16px;font-weight:700;color:#1e40af;border-left:2px solid #e4e8f0;">
                                <?= $etiquetasRoles[$rol] ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                    <tr style="background:#f8fafc;border-top:1px solid #e4e8f0;">
                        <th style="padding:8px 20px;color:#64748b;font-size:.78rem;font-weight:600;"></th>
                        <?php foreach ($roles as $rol): ?>
                            <th class="text-center" style="padding:8px 12px;border-left:2px solid #e4e8f0;color:#64748b;font-size:.78rem;font-weight:700;">
                                <i class="mdi mdi-email-outline"></i> Email
                            </th>
                            <th class="text-center" style="padding:8px 12px;color:#64748b;font-size:.78rem;font-weight:700;">
                                <i class="mdi mdi-bell-outline"></i> In-App
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($eventos as $eventoKey => $eventoMeta): ?>
                        <tr style="border-top:1px solid #f1f5f9;">
                            <td style="padding:14px 20px;">
                                <div class="d-flex align-items-center">
                                    <span class="mdi <?= $eventoMeta['icono'] ?>" style="font-size:1.1rem;color:#1e40af;margin-right:8px;"></span>
                                    <span style="font-weight:600;"><?= htmlspecialchars($eventoMeta['label']) ?></span>
                                </div>
                            </td>
                            <?php foreach ($roles as $rol): ?>
                                <?php
                                    $pref  = $prefsRoles[$rol][$eventoKey] ?? null;
                                    $email = $pref ? (bool)$pref->canal_email : false;
                                    $inapp = $pref ? (bool)$pref->canal_inapp : true;
                                    $nameEmail = "notif_{$rol}_{$eventoKey}_email";
                                    $nameInapp = "notif_{$rol}_{$eventoKey}_inapp";
                                ?>
                                <td class="text-center" style="border-left:2px solid #e4e8f0;padding:14px 12px;">
                                    <div class="custom-control custom-switch d-inline-block">
                                        <input type="checkbox" class="custom-control-input" id="<?= $nameEmail ?>"
                                               name="<?= $nameEmail ?>" <?= $email ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="<?= $nameEmail ?>"></label>
                                    </div>
                                </td>
                                <td class="text-center" style="padding:14px 12px;">
                                    <div class="custom-control custom-switch d-inline-block">
                                        <input type="checkbox" class="custom-control-input" id="<?= $nameInapp ?>"
                                               name="<?= $nameInapp ?>" <?= $inapp ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="<?= $nameInapp ?>"></label>
                                    </div>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary" style="border-radius:8px;font-weight:600;padding:10px 28px;">
            <i class="mdi mdi-content-save mr-1"></i> Guardar preferencias
        </button>
    </div>
</form>

<?php endif; ?>

<!-- Script para probar SMTP -->
<script>
$(document).ready(function() {
    $('#btnProbarSmtp').on('click', function() {
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin mr-1"></i> Probando...');
        $.get('<?= BASE_URL ?>/index.php?controller=configuracion&action=probarSmtp', function(res) {
            var html = res.ok
                ? '<div class="alert alert-success mb-0" style="border-radius:8px;"><i class="mdi mdi-check-circle mr-1"></i>' + res.msg + '</div>'
                : '<div class="alert alert-danger mb-0" style="border-radius:8px;"><i class="mdi mdi-alert mr-1"></i>' + res.msg + '</div>';
            $('#smtpTestResult').html(html).show();
        }, 'json').fail(function() {
            $('#smtpTestResult').html('<div class="alert alert-danger mb-0" style="border-radius:8px;"><i class="mdi mdi-alert mr-1"></i> Error de comunicación.</div>').show();
        }).always(function() {
            btn.prop('disabled', false).html('<i class="mdi mdi-send-check-outline mr-1"></i> Probar conexión');
        });
    });
});
</script>
