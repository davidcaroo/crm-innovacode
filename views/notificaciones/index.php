<?php
// views/notificaciones/index.php
// Variables: $notificaciones (array de objetos)
?>

<div class="page-header mb-4">
    <div>
        <h2 class="page-title"><span class="mdi mdi-bell-outline"></span> Notificaciones</h2>
        <span class="page-subtitle">Tu bandeja de actividad reciente</span>
    </div>
    <form method="post" action="<?= BASE_URL ?>/index.php?controller=notificacion&action=marcarTodas">
        <button type="submit" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;font-weight:600;">
            <i class="mdi mdi-check-all mr-1"></i> Marcar todas como leídas
        </button>
    </form>
</div>

<?php
$iconosTipo = [
    'venta_ganada'     => ['icono' => 'mdi-cash-check',            'color' => '#15803d', 'bg' => '#dcfce7'],
    'empresa_creada'   => ['icono' => 'mdi-domain',                'color' => '#1e40af', 'bg' => '#dbeafe'],
    'cambio_etapa'     => ['icono' => 'mdi-arrow-right-circle',    'color' => '#7c3aed', 'bg' => '#ede9fe'],
    'credito_aprobado' => ['icono' => 'mdi-credit-card-check',     'color' => '#b45309', 'bg' => '#fef3c7'],
];
?>

<div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
    <?php if (empty($notificaciones)): ?>
        <div class="text-center py-5">
            <span class="mdi mdi-bell-off-outline" style="font-size:3rem;color:#cbd5e1;display:block;margin-bottom:12px;"></span>
            <p class="text-muted mb-0">No tienes notificaciones aún.</p>
        </div>
    <?php else: ?>
        <div class="list-group list-group-flush">
            <?php foreach ($notificaciones as $notif):
                $meta  = $iconosTipo[$notif->tipo] ?? ['icono' => 'mdi-bell', 'color' => '#1e40af', 'bg' => '#dbeafe'];
                $fecha = date('d M Y, H:i', strtotime($notif->creado_en));
                $url   = !empty($notif->url_accion) ? $notif->url_accion : '#';
            ?>
            <a href="<?= htmlspecialchars($url) ?>"
               class="list-group-item list-group-item-action d-flex align-items-start py-3 px-4"
               style="border:none;border-bottom:1px solid #f1f5f9;<?= !$notif->leida ? 'background:#f8faff;' : '' ?>">
                <!-- Icono -->
                <div class="mr-3 mt-1 flex-shrink-0" style="width:40px;height:40px;border-radius:10px;background:<?= $meta['bg'] ?>;display:flex;align-items:center;justify-content:center;">
                    <span class="mdi <?= $meta['icono'] ?>" style="font-size:1.3rem;color:<?= $meta['color'] ?>;"></span>
                </div>
                <!-- Texto -->
                <div class="flex-grow-1">
                    <div style="font-weight:<?= !$notif->leida ? '700' : '500' ?>;color:#1e293b;font-size:.93rem;">
                        <?= htmlspecialchars($notif->titulo) ?>
                        <?php if (!$notif->leida): ?>
                            <span class="badge badge-primary ml-1" style="font-size:.65rem;border-radius:6px;">Nueva</span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($notif->mensaje)): ?>
                        <div style="font-size:.83rem;color:#64748b;margin-top:2px;"><?= htmlspecialchars($notif->mensaje) ?></div>
                    <?php endif; ?>
                    <div style="font-size:.78rem;color:#94a3b8;margin-top:4px;">
                        <span class="mdi mdi-clock-outline mr-1"></span><?= $fecha ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
