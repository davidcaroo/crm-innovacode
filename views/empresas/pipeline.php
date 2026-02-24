<?php // Layout included from BaseController 
?>

<?php
$etapasConfig = [
    'prospectado' => ['label' => 'Prospectado',  'icon' => 'mdi-magnify',        'cls' => 'ph-prospectado',  'badge' => 'badge-prospectado'],
    'contactado'  => ['label' => 'Contactado',   'icon' => 'mdi-phone',          'cls' => 'ph-contactado',   'badge' => 'badge-contactado'],
    'negociacion' => ['label' => 'Negociacion',  'icon' => 'mdi-handshake',      'cls' => 'ph-negociacion',  'badge' => 'badge-negociacion'],
    'ganado'      => ['label' => 'Ganado',       'icon' => 'mdi-trophy',         'cls' => 'ph-ganado',       'badge' => 'badge-ganado'],
    'perdido'     => ['label' => 'Perdido',      'icon' => 'mdi-close-circle',   'cls' => 'ph-perdido',      'badge' => 'badge-perdido'],
];
$total = array_sum(array_map('count', $etapas));
?>

<div class="page-header">
    <div>
        <h2 class="page-title"><span class="mdi mdi-view-column"></span> Pipeline de Ventas</h2>
        <span class="page-subtitle"><?php echo $total; ?> empresa<?php echo $total != 1 ? 's' : ''; ?> en total</span>
    </div>
    <a href="<?php echo url('empresa/crear'); ?>" class="btn btn-primary btn-sm">
        <span class="mdi mdi-plus"></span> Nueva Empresa
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body" style="padding:16px; overflow-x:auto;">
        <div class="pipeline-board">
            <?php foreach ($etapasConfig as $key => $cfg):
                $lista = $etapas[$key] ?? [];
                $count = count($lista);
            ?>
                <div class="pipeline-col">
                    <div class="pipeline-header <?php echo $cfg['cls']; ?>">
                        <span><span class="mdi <?php echo $cfg['icon']; ?>"></span> <?php echo $cfg['label']; ?></span>
                        <span class="badge-count"><?php echo $count; ?></span>
                    </div>
                    <div class="pipeline-body">
                        <?php if (empty($lista)): ?>
                            <div style="color:#94a3b8;text-align:center;font-size:0.85rem;padding:18px 0;">Sin empresas</div>
                        <?php else: ?>
                            <?php foreach ($lista as $emp): ?>
                                <div class="pipeline-card">
                                    <div class="empresa-name" title="<?php echo htmlspecialchars($emp->razon_social); ?>">
                                        <?php echo htmlspecialchars($emp->razon_social); ?>
                                    </div>
                                    <div class="empresa-meta">
                                        <?php if ($emp->ciudad): ?>
                                            <span class="mdi mdi-map-marker" style="font-size:0.85rem;"></span> <?php echo htmlspecialchars($emp->ciudad); ?>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($emp->actividad_economica): ?>
                                        <div class="empresa-meta mt-1" style="font-style:italic;">
                                            <?php echo htmlspecialchars(mb_strimwidth($emp->actividad_economica, 0, 30, '...')); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-flex mt-2" style="gap:4px;">
                                        <a href="<?php echo url('trazabilidad/index', ['empresa_id' => $emp->id]); ?>"
                                            class="btn btn-sm btn-outline-success" title="Trazabilidad"
                                            style="padding:2px 7px;font-size:0.8rem;border-radius:5px; border-color: #28a745;">
                                            <i class="bi bi-clock-history" style="color: #28a745; font-weight: bold;"></i>
                                        </a>
                                        <a href="<?php echo url('contacto/index', ['empresa_id' => $emp->id]); ?>"
                                            class="btn btn-sm btn-outline-info" title="Contactos"
                                            style="padding:2px 7px;font-size:0.8rem;border-radius:5px;">
                                            <i class="bi bi-people-fill"></i>
                                        </a>
                                        <a href="<?php echo url('empresa/editar', ['id' => $emp->id]); ?>"
                                            class="btn btn-sm btn-outline-primary" title="Editar"
                                            style="padding:2px 7px;font-size:0.8rem;border-radius:5px;">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <a href="#"
                                            class="btn btn-sm btn-outline-danger" title="Eliminar"
                                            style="padding:2px 7px;font-size:0.8rem;border-radius:5px;"
                                            onclick="return confirmarEliminacion('<?php echo url('empresa/eliminar', ['id' => $emp->id]); ?>', '¿Eliminar la empresa <?php echo htmlspecialchars($emp->razon_social); ?>?')">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php // Footer included from BaseController 
?>