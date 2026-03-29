<?php
// views/notificaciones/index.php
// Variables: $notificaciones (array de objetos)
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-bell mr-2"></i> Notificaciones</h1>
    <form method="post" action="<?= url('notificacion/marcarTodas') ?>" class="d-inline">
        <button type="submit" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
            <i class="fas fa-check-double fa-sm text-white-50 mr-1"></i> Marcar todas como leídas
        </button>
    </form>
</div>

<?php
$iconosTipo = [
    'venta_ganada'     => ['icono' => 'fas fa-dollar-sign', 'bg' => 'success'],
    'empresa_creada'   => ['icono' => 'fas fa-building', 'bg' => 'primary'],
    'cambio_etapa'     => ['icono' => 'fas fa-random', 'bg' => 'info'],
    'credito_aprobado' => ['icono' => 'fas fa-credit-card', 'bg' => 'warning'],
];
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tu bandeja de actividad reciente</h6>
    </div>
    <div class="card-body p-0">
        <?php if (empty($notificaciones)): ?>
            <div class="text-center py-5">
                <i class="far fa-bell-slash text-gray-300 mb-3" style="font-size:3rem;"></i>
                <p class="text-muted mb-0">No tienes notificaciones aún.</p>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($notificaciones as $notif):
                    $meta  = $iconosTipo[$notif->tipo] ?? ['icono' => 'fas fa-bell', 'bg' => 'primary'];
                    $fecha = date('d M Y, H:i', strtotime($notif->creado_en));
                    $url   = !empty($notif->url_accion) ? $notif->url_accion : '#';
                ?>
                    <a href="<?= htmlspecialchars($url) ?>"
                        class="list-group-item list-group-item-action d-flex align-items-start py-3 px-4 <?= !$notif->leida ? 'bg-light' : '' ?>"
                        style="border-left: 0; border-right: 0; <?php if(array_key_last($notificaciones) === array_search($notif, $notificaciones)) echo 'border-bottom: 0;'; ?>">
                        
                        <!-- Icono -->
                        <div class="mr-3 flex-shrink-0">
                            <div class="icon-circle bg-<?= $meta['bg'] ?>">
                                <i class="<?= $meta['icono'] ?> text-white"></i>
                            </div>
                        </div>
                        
                        <!-- Texto -->
                        <div class="flex-grow-1">
                            <div class="font-weight-<?= !$notif->leida ? 'bold' : 'normal' ?> text-gray-800">
                                <?= htmlspecialchars($notif->titulo) ?>
                                <?php if (!$notif->leida): ?>
                                    <span class="badge badge-danger ml-1">Nueva</span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($notif->mensaje)): ?>
                                <div class="small text-gray-600 mt-1"><?= htmlspecialchars($notif->mensaje) ?></div>
                            <?php endif; ?>
                            <div class="small text-gray-500 mt-1">
                                <i class="far fa-clock mr-1"></i><?= $fecha ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>