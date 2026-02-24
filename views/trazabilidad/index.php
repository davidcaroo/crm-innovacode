<?php // Layout included from BaseController 
?>

<?php
$tipoConfig = [
    'llamada'  => ['label' => 'Llamada',  'ico' => 'mdi-phone',         'color' => '#3b82f6'],
    'correo'   => ['label' => 'Correo',   'ico' => 'mdi-email',         'color' => '#8b5cf6'],
    'reunion'  => ['label' => 'Reunion',  'ico' => 'mdi-account-group', 'color' => '#f59e0b'],
    'visita'   => ['label' => 'Visita',   'ico' => 'mdi-map-marker',    'color' => '#22c55e'],
    'nota'     => ['label' => 'Nota',     'ico' => 'mdi-note-text',     'color' => '#94a3b8'],
];
$etapaColors = ['prospectado' => 'badge-prospectado', 'contactado' => 'badge-contactado', 'negociacion' => 'badge-negociacion', 'ganado' => 'badge-ganado', 'perdido' => 'badge-perdido'];
?>

<div class="page-header">
    <div>
        <h2 class="page-title"><span class="mdi mdi-timeline-text" style="color: #28a745;"></span> Trazabilidad</h2>
        <?php if (isset($empresa)): ?>
            <span class="page-subtitle">
                <?php echo htmlspecialchars($empresa->razon_social); ?> &mdash;
                <span class="badge-etapa <?= $etapaColors[$empresa->etapa_venta] ?? '' ?>">
                    <?= ucfirst($empresa->etapa_venta) ?>
                </span>
            </span>
        <?php endif; ?>
    </div>
    <div class="d-flex" style="gap:8px;">
        <a href="<?php echo url('trazabilidad/registrar', ['empresa_id' => $empresa_id]); ?>"
            class="btn btn-primary btn-sm">
            <span class="mdi mdi-plus"></span> Nueva Actividad
        </a>
        <a href="<?php echo url('empresa/index'); ?>"
            class="btn btn-outline-secondary btn-sm">
            <span class="mdi mdi-arrow-left"></span> Empresas
        </a>
    </div>
</div>

<?php if (empty($historial)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center text-muted py-5">
            <span class="mdi mdi-timeline-text" style="font-size:2.5rem;opacity:0.3;"></span>
            <p class="mt-2">Sin actividades registradas. <a href="<?php echo url('trazabilidad/registrar', ['empresa_id' => $empresa_id]); ?>">Registrar primera actividad</a></p>
        </div>
    </div>
<?php else: ?>
    <!-- Timeline -->
    <div style="max-width:750px;">
        <?php foreach ($historial as $h):
            $tipo = $tipoConfig[$h->tipo_actividad ?? 'nota'] ?? $tipoConfig['nota'];
            $badgeEtapa = $etapaColors[$h->etapa_venta] ?? '';
        ?>
            <div class="d-flex mb-3" style="gap:12px;">
                <div style="flex:0 0 36px;text-align:center;">
                    <div style="width:36px;height:36px;border-radius:50%;background:<?= $tipo['color'] ?>18;border:2px solid <?= $tipo['color'] ?>;display:flex;align-items:center;justify-content:center;">
                        <span class="mdi <?= $tipo['ico'] ?>" style="color:<?= $tipo['color'] ?>;font-size:1.1rem;"></span>
                    </div>
                </div>
                <div class="card border-0 shadow-sm flex-fill" style="margin:0;">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div>
                                <span style="font-weight:700;font-size:0.9rem;color:<?= $tipo['color'] ?>;"><?= $tipo['label'] ?></span>
                                &nbsp;&rarr;&nbsp;
                                <span class="badge-etapa <?= $badgeEtapa ?>"><?= ucfirst($h->etapa_venta) ?></span>
                            </div>
                            <small class="text-muted"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($h->fecha))) ?></small>
                        </div>
                        <?php if ($h->observaciones): ?>
                            <p class="mb-1" style="font-size:0.9rem;color:#475569;"><?= nl2br(htmlspecialchars($h->observaciones)) ?></p>
                        <?php endif; ?>
                        <small class="text-muted"><span class="mdi mdi-account"></span> <?= htmlspecialchars($h->usuario) ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php // Footer included from BaseController 
?>