<?php
// views/notificaciones/index.php
// Variables: $notificaciones (array de objetos)
?>

<div class="page-header mb-4">
    <div>
        <h2 class="page-title"><i class="fa-regular fa-bell"></i> Notificaciones</h2>
        <span class="page-subtitle">Tu bandeja de actividad reciente</span>
    </div>
    <form method="post" action="<?= url('notificacion/marcarTodas') ?>">
        <button type="submit" class="btn btn-sm btn-success fw-bold">
            <i class="fa-solid fa-check-double me-1"></i> Marcar todas como leídas
        </button>
    </form>
</div>

<?php
$iconosTipo = [
    'venta_ganada'     => ['icono' => 'fa-solid fa-sack-dollar', 'color' => '#15803d', 'bg' => '#dcfce7'],
    'empresa_creada'   => ['icono' => 'fa-solid fa-building', 'color' => '#1e40af', 'bg' => '#dbeafe'],
    'cambio_etapa'     => ['icono' => 'fa-solid fa-right-left', 'color' => '#7c3aed', 'bg' => '#ede9fe'],
    'credito_aprobado' => ['icono' => 'fa-solid fa-credit-card', 'color' => '#b45309', 'bg' => '#fef3c7'],
];
?>

<div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
    <?php if (empty($notificaciones)): ?>
        <div class="text-center py-5">
            <i class="fa-regular fa-bell-slash" style="font-size:3rem;color:#cbd5e1;display:block;margin-bottom:12px;"></i>
            <p class="text-muted mb-0">No tienes notificaciones aún.</p>
        </div>
    <?php else: ?>
        <div class="list-group list-group-flush">
            <?php foreach ($notificaciones as $notif):
                $meta  = $iconosTipo[$notif->tipo] ?? ['icono' => 'fa-regular fa-bell', 'color' => '#1e40af', 'bg' => '#dbeafe'];
                $fecha = date('d M Y, H:i', strtotime($notif->creado_en));
                $url   = !empty($notif->url_accion) ? $notif->url_accion : '#';
            ?>
                <a href="<?= htmlspecialchars($url) ?>"
                    class="list-group-item list-group-item-action d-flex align-items-start py-3 px-4"
                    style="border:none;border-bottom:1px solid #f1f5f9;<?= !$notif->leida ? 'background:#f8faff;' : '' ?>">
                    <!-- Icono -->
                    <div class="me-3 mt-1 flex-shrink-0" style="width:40px;height:40px;border-radius:10px;background:<?= $meta['bg'] ?>;display:flex;align-items:center;justify-content:center;">
                        <i class="<?= $meta['icono'] ?>" style="font-size:1rem;color:<?= $meta['color'] ?>;"></i>
                    </div>
                    <!-- Texto -->
                    <div class="flex-grow-1">
                        <div style="font-weight:<?= !$notif->leida ? '700' : '500' ?>;color:#1e293b;font-size:.93rem;">
                            <?= htmlspecialchars($notif->titulo) ?>
                            <?php if (!$notif->leida): ?>
                                <span class="badge bg-primary ms-1" style="font-size:.65rem;border-radius:6px;">Nueva</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($notif->mensaje)): ?>
                            <div style="font-size:.83rem;color:#64748b;margin-top:2px;"><?= htmlspecialchars($notif->mensaje) ?></div>
                        <?php endif; ?>
                        <div style="font-size:.78rem;color:#94a3b8;margin-top:4px;">
                            <i class="fa-regular fa-clock me-1"></i><?= $fecha ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>