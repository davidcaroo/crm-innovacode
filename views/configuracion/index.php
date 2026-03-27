<?php
// views/configuracion/index.php
// Variables: $smtp, $integraciones, $eventos, $prefsRoles, $roles, $tab
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Configuración Avanzada</h1>
        <p class="mb-0 small text-muted">Gestiona SMTP, integraciones externas y preferencias de notificaciones</p>
    </div>
</div>

<?php
$okMsgs = [
    'smtp'  => '<i class="fas fa-check-circle mr-1"></i> Configuración SMTP guardada correctamente.',
    'int'   => '<i class="fas fa-check-circle mr-1"></i> Integración guardada correctamente.',
    'notif' => '<i class="fas fa-check-circle mr-1"></i> Preferencias de notificación actualizadas.',
];
$ok = $_GET['ok'] ?? null;
if ($ok && isset($okMsgs[$ok])):
?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" style="border-radius:10px;border:none;">
        <?= $okMsgs[$ok] ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- TABS NAVEGACIÓN -->
<ul class="nav nav-tabs" id="configTabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'smtp' ? 'active' : '' ?>"
            href="<?= url('configuracion/editar', ['tab' => 'smtp']) ?>"
            role="tab" aria-selected="<?= $tab === 'smtp' ? 'true' : 'false' ?>">
            <i class="far fa-envelope mr-1"></i> Comunicaciones
            <?php if (!Configuracion::smtpConfigurado()): ?>
                <span class="badge badge-warning text-dark ml-1">Sin configurar</span>
            <?php else: ?>
                <span class="badge badge-success ml-1">Activo</span>
            <?php endif; ?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'integraciones' ? 'active' : '' ?>"
            href="<?= url('configuracion/editar', ['tab' => 'integraciones']) ?>"
            role="tab" aria-selected="<?= $tab === 'integraciones' ? 'true' : 'false' ?>">
            <i class="fas fa-plug mr-1"></i> Integraciones
            <?php
            $activasCount = count(array_filter((array)$integraciones, fn($i) => $i->estado === 'activa'));
            ?>
            <?php if ($activasCount > 0): ?>
                <span class="badge badge-primary ml-1"><?= $activasCount ?> activa<?= $activasCount !== 1 ? 's' : '' ?></span>
            <?php endif; ?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'notificaciones' ? 'active' : '' ?>"
            href="<?= url('configuracion/editar', ['tab' => 'notificaciones']) ?>"
            role="tab" aria-selected="<?= $tab === 'notificaciones' ? 'true' : 'false' ?>">
            <i class="far fa-bell mr-1"></i> Notificaciones
        </a>
    </li>
</ul>

<div class="tab-content mt-3" id="configTabsContent">

    <!-- ====================================================== -->
    <!-- TAB: COMUNICACIONES / SMTP                              -->
    <!-- ====================================================== -->
    <?php if ($tab === 'smtp'): ?>
        <div class="tab-pane fade show active" id="smtp" role="tabpanel">
            <div class="row">
                <div class="col-12 col-lg-7">
                    <div class="card shadow mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <i class="fas fa-paper-plane" style="font-size:1.5rem;color:#1e40af;margin-right:12px;"></i>
                                <div>
                                    <h5 class="mb-0" style="font-weight:700;color:#1e40af;">Configuración SMTP</h5>
                                    <small class="text-muted">Necesario para envío de correos y alertas del sistema.</small>
                                </div>
                            </div>

                            <?php if (!Configuracion::smtpConfigurado()): ?>
                                <div class="alert alert-warning" style="border-radius:10px;border:none;">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <strong>SMTP sin configurar.</strong> Las notificaciones por email no funcionarán hasta que completes este formulario.
                                </div>
                            <?php endif; ?>

                            <form method="post" action="<?= url('configuracion/guardarSmtp') ?>" autocomplete="off">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="font-weight-bold" style="font-size:.85rem;">Host SMTP</label>
                                            <input type="text" class="form-control" name="smtp_host"
                                                value="<?= htmlspecialchars($smtp['smtp_host'] ?? '') ?>"
                                                placeholder="smtp.gmail.com">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="font-weight-bold" style="font-size:.85rem;">Puerto</label>
                                            <input type="number" class="form-control" name="smtp_port"
                                                value="<?= htmlspecialchars($smtp['smtp_port'] ?? 587) ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="font-weight-bold" style="font-size:.85rem;">Usuario / Email</label>
                                    <input type="email" class="form-control" name="smtp_user"
                                        value="<?= htmlspecialchars($smtp['smtp_user'] ?? '') ?>"
                                        placeholder="tu@empresa.com">
                                </div>
                                <div class="mb-3">
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

                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary" style="border-radius:8px;font-weight:600;">
                                        <i class="fas fa-save mr-1"></i> Guardar SMTP
                                    </button>
                                    <button type="button" id="btnProbarSmtp" class="btn btn-success ml-2" style="border-radius:8px;font-weight:700;color:#fff;">
                                        <i class="fas fa-vials mr-1"></i> Probar conexión
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
                            <h6 class="font-weight-bold mb-3" style="color:#1e40af;"><i class="far fa-lightbulb mr-1"></i> Guía de configuración</h6>
                            <p style="font-size:.88rem;color:#374151;"><strong>Gmail:</strong><br>Host: <code>smtp.gmail.com</code>, Puerto: <code>587</code><br>Activar "Contraseñas de aplicación" en tu cuenta Google.</p>
                            <p style="font-size:.88rem;color:#374151;"><strong>Outlook/Hotmail:</strong><br>Host: <code>smtp-mail.outlook.com</code>, Puerto: <code>587</code></p>
                            <p style="font-size:.88rem;color:#374151;"><strong>SendGrid:</strong><br>Host: <code>smtp.sendgrid.net</code>, Puerto: <code>587</code><br>Usuario: <code>apikey</code>, Contraseña: tu API Key.</p>
                            <hr>
                            <p class="mb-0" style="font-size:.82rem;color:#64748b;"><i class="fas fa-shield-alt mr-1"></i> La contraseña se almacena cifrada con AES-256-CBC.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ====================================================== -->
    <!-- TAB: INTEGRACIONES                                       -->
    <!-- ====================================================== -->
    <?php if ($tab === 'integraciones'): ?>
        <div class="tab-pane fade show active" id="integraciones" role="tabpanel">

            <?php
            $iconosIntegracion = [
                'whatsapp'         => ['icono' => 'fab fa-whatsapp', 'color' => '#25D366', 'bg' => '#e8fdf1'],
                'google_calendar'  => ['icono' => 'far fa-calendar-check', 'color' => '#4285F4', 'bg' => '#e8f0fe'],
                'google_maps'      => ['icono' => 'fas fa-map-marker-alt', 'color' => '#EA4335', 'bg' => '#fde8e8'],
                'webhook_generico' => ['icono' => 'fas fa-link', 'color' => '#7c3aed', 'bg' => '#f3eeff'],
                'zapier'           => ['icono' => 'fas fa-bolt', 'color' => '#FF4A00', 'bg' => '#fff0eb'],
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
                    $meta     = $iconosIntegracion[$integ->slug] ?? ['icono' => 'fas fa-plug', 'color' => '#1e40af', 'bg' => '#e8f0fe'];
                    $campos_c = $camposPorSlug[$integ->slug] ?? [];
                    $vals     = !empty($integ->campos) ? (json_decode(Configuracion::desencriptar($integ->campos), true) ?? []) : [];
                ?>
                    <div class="col-12 col-md-6 col-xl-4 mb-4">
                        <div class="card shadow h-100" style="border-radius:14px;border-top:3px solid <?= $meta['color'] ?>!important;">
                            <div class="card-body p-4">
                                <!-- Cabecera -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="<?= $meta['icono'] ?>" style="font-size:1.7rem;color:<?= $meta['color'] ?>;margin-right:10px;"></i>
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
                                <form method="post" action="<?= url('configuracion/guardarIntegracion') ?>">
                                    <input type="hidden" name="slug" value="<?= htmlspecialchars($integ->slug) ?>">

                                    <?php foreach ($campos_c as $c): ?>
                                        <div class="mb-2">
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

                                    <button type="submit" class="btn btn-sm btn-primary w-100" style="border-radius:8px;font-weight:600;">
                                        <i class="fas fa-save mr-1"></i> Guardar
                                    </button>
                                </form>

                                <?php if ($integ->estado === 'error' && !empty($integ->error_msg)): ?>
                                    <div class="alert alert-danger mt-2 mb-0 p-2" style="border-radius:8px;font-size:.8rem;">
                                        <i class="fas fa-exclamation-triangle mr-1"></i><?= htmlspecialchars($integ->error_msg) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="card border-0 shadow-sm mt-2" style="border-radius:12px;background:#f8fafc;">
                <div class="card-body p-3">
                    <small class="text-muted"><i class="fas fa-lock mr-1"></i> Todos los campos sensibles (API Keys) se cifran con AES-256-CBC antes de guardarse en la base de datos.</small>
                </div>
            </div>

        </div>

    <?php endif; ?>

    <!-- ====================================================== -->
    <!-- TAB: NOTIFICACIONES                                      -->
    <!-- ====================================================== -->
    <?php if ($tab === 'notificaciones'): ?>
        <div class="tab-pane fade show active" id="notificaciones" role="tabpanel">

            <?php
            $etiquetasRoles = ['superadmin' => 'Superadmin', 'admin' => 'Admin', 'usuario' => 'Vendedor'];
            $eventoIconosFa = [
                'venta_ganada' => 'fas fa-dollar-sign',
                'empresa_creada' => 'fas fa-building',
                'cambio_etapa' => 'fas fa-exchange-alt',
                'credito_aprobado' => 'fas fa-credit-card',
            ];
            ?>

            <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-1">
                        <i class="fas fa-bell mr-2" style="font-size:1.2rem;color:#1e40af;"></i>
                        <div>
                            <h5 class="mb-0" style="font-weight:700;color:#1e40af;">Reglas de Notificación por Rol</h5>
                            <small class="text-muted">Define qué eventos notifican a cada rol y por qué canal.</small>
                        </div>
                    </div>
                    <?php if (!Configuracion::smtpConfigurado()): ?>
                        <div class="alert alert-warning mt-3 mb-0" style="border-radius:10px;border:none;font-size:.88rem;">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <strong>Advertencia:</strong> El canal Email no funcionará hasta que configures el SMTP en la pestaña <a href="?controller=configuracion&action=editar&tab=smtp">Comunicaciones</a>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <form method="post" action="<?= url('configuracion/guardarNotificaciones') ?>">
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
                                            <i class="far fa-envelope"></i> Email
                                        </th>
                                        <th class="text-center" style="padding:8px 12px;color:#64748b;font-size:.78rem;font-weight:700;">
                                            <i class="far fa-bell"></i> In-App
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($eventos as $eventoKey => $eventoMeta): ?>
                                    <tr style="border-top:1px solid #f1f5f9;">
                                        <td style="padding:14px 20px;">
                                            <div class="d-flex align-items-center">
                                                <?php $eventoIcono = $eventoIconosFa[$eventoKey] ?? 'far fa-bell'; ?>
                                                <i class="<?= $eventoIcono ?>" style="font-size:1rem;color:#1e40af;margin-right:8px;"></i>
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
                        <i class="fas fa-save mr-1"></i> Guardar preferencias
                    </button>
                </div>
            </form>

        </div>

    <?php endif; ?>

</div>

<!-- Script para probar SMTP -->
<script>
    $(document).ready(function() {
        $('#btnProbarSmtp').on('click', function() {
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Conectando...');
            $('#smtpTestResult').hide().html('');
            $.ajax({
                url: '<?= url('configuracion/probarSmtp') ?>',
                type: 'GET',
                dataType: 'json',
                timeout: 30000,
                success: function(res) {
                    var icon = res.ok ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
                    var cls = res.ok ? 'alert-success' : 'alert-danger';
                    var html = '<div class="alert ' + cls + ' mb-0" style="border-radius:8px;">' +
                        '<i class="' + icon + ' mr-1"></i>' + res.msg + '</div>';
                    if (res.log) {
                        html += '<details class="mt-2" style="font-size:0.8rem;"><summary style="cursor:pointer;color:#64748b;">Ver log de conexión</summary>' +
                            '<pre style="background:#1e293b;color:#94a3b8;border-radius:6px;padding:10px;font-size:0.78rem;overflow:auto;max-height:180px;margin-top:6px;">' + res.log + '</pre></details>';
                    }
                    $('#smtpTestResult').html(html).show();
                },
                error: function(xhr) {
                    $('#smtpTestResult').html('<div class="alert alert-danger mb-0" style="border-radius:8px;"><i class="fas fa-exclamation-triangle mr-1"></i> Error de comunicación con el servidor (' + (xhr.status || 'timeout') + ').</div>').show();
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-vials mr-1"></i> Probar conexión');
                }
            });
        });
    });
</script>