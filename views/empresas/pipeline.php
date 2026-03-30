<?php // Layout included from BaseController 
?>

<?php
$etapasConfig = [
    'prospectado' => ['label' => 'Investigación previa',  'icon' => 'fas fa-search'],
    'contactado'  => ['label' => 'Contacto interesado',   'icon' => 'fas fa-phone'],
    'negociacion' => ['label' => 'Negociacion',  'icon' => 'fas fa-handshake'],
    'seguimiento' => ['label' => 'Seguimiento',  'icon' => 'fas fa-eye'],
    'ganado'      => ['label' => 'Ganado',       'icon' => 'fas fa-trophy'],
    'perdido'     => ['label' => 'Perdido',      'icon' => 'fas fa-times-circle'],
];
$total = array_sum($conteosPorEtapa); // Usar $conteosPorEtapa desde el controlador
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Pipeline de Ventas</h1>
    <a href="<?php echo url('empresa/crear'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Nueva Empresa
    </a>
</div>

<div class="row">
    <?php
    $laneMap = [
        'prospectado' => 'info',
        'contactado' => 'warning',
        'negociacion' => 'primary',
        'ganado' => 'success',
        'perdido' => 'danger',
    ];
    ?>
    <?php foreach ($etapasConfig as $key => $cfg):
        $lista = $etapas[$key] ?? [];
        $count = $conteosPorEtapa[$key] ?? 0;
        $laneColor = $laneMap[$key] ?? 'secondary';
    ?>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-<?php echo $laneColor; ?> shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="m-0 font-weight-bold text-<?php echo $laneColor; ?>"><i class="<?php echo $cfg['icon']; ?>"></i> <?php echo $cfg['label']; ?></h6>
                        <span class="badge badge-pill badge-<?php echo $laneColor; ?>"><?php echo $count; ?></span>
                    </div>

                    <?php if (empty($lista)): ?>
                        <div class="text-center text-muted py-3">Sin empresas</div>
                    <?php else: ?>
                        <?php foreach ($lista as $emp): ?>
                            <?php
                            $estadoFlujo = $estadosTrazabilidad[(int)$emp->id] ?? [
                                'tiene_estudio_necesidades' => false,
                                'tiene_oferta_servicios' => false,
                                'tiene_seguimiento_oferta' => false,
                            ];
                            $etapaActual = strtolower((string)($emp->etapa_venta ?? ''));
                            $contactoEfectivo = (
                                $etapaActual === 'contactado'
                                && strtoupper(trim((string)($emp->aplica ?? ''))) === 'SI'
                            );
                            $tieneOferta = !empty($estadoFlujo['tiene_oferta_servicios']);
                            $tieneEstudio = !empty($estadoFlujo['tiene_estudio_necesidades']);
                            $tieneSeguimiento = !empty($estadoFlujo['tiene_seguimiento_oferta']);
                            $mostrarBadgesActividad = !in_array($etapaActual, ['ganado', 'perdido']);
                            ?>
                            <div class="card shadow mb-2">
                                <div class="card-body p-2">
                                    <div class="font-weight-bold text-primary mb-1" title="<?php echo htmlspecialchars($emp->razon_social); ?>">
                                        <?php echo htmlspecialchars(mb_strimwidth($emp->razon_social, 0, 30, '...')); ?>
                                    </div>

                                    <?php if ($mostrarBadgesActividad): ?>
                                        <?php if ($tieneSeguimiento): ?>
                                            <div class="mb-1">
                                                <span class="badge badge-info bg-info text-white">Seguimiento de la oferta</span>
                                            </div>
                                        <?php elseif ($tieneOferta): ?>
                                            <div class="mb-1">
                                                <span class="badge badge-primary">Oferta de servicios</span>
                                            </div>
                                        <?php elseif ($tieneEstudio): ?>
                                            <div class="mb-1">
                                                <span class="badge badge-primary">Estudio de necesidades</span>
                                            </div>
                                        <?php elseif ($contactoEfectivo): ?>
                                            <div class="mb-1">
                                                <span class="badge badge-success mr-1">Contacto interesado</span>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <div class="small text-gray-600 mb-1">
                                        <?php if ($emp->ciudad): ?><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($emp->ciudad); ?><?php endif; ?>
                                    </div>
                                    <?php if ($emp->actividad_economica): ?>
                                        <div class="small text-muted mb-2">
                                            <?php echo htmlspecialchars(mb_strimwidth($emp->actividad_economica, 0, 30, '...')); ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?php echo url('trazabilidad/index', ['empresa_id' => $emp->id]); ?>" class="btn btn-sm btn-outline-success" title="Trazabilidad"><i class="fas fa-eye fa-sm"></i></a>
                                        <a href="<?php echo url('contacto/index', ['empresa_id' => $emp->id]); ?>" class="btn btn-sm btn-outline-info" title="Contactos"><i class="fas fa-address-book fa-sm"></i></a>
                                        <a href="<?php echo url('empresa/editar', ['id' => $emp->id]); ?>" class="btn btn-sm btn-outline-primary" title="Editar"><i class="fas fa-edit fa-sm"></i></a>
                                        <a href="#" class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="return confirmarEliminacion('<?php echo url('empresa/eliminar', ['id' => $emp->id]); ?>', '¿Eliminar la empresa <?php echo htmlspecialchars($emp->razon_social); ?>?')"><i class="fas fa-trash-alt fa-sm"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Paginación Global del Pipeline -->
<?php if (isset($totalPages) && $totalPages > 1): ?>
    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-center">
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?controller=empresa&action=pipeline&page=<?php echo $currentPage - 1; ?>" tabindex="-1">Anterior</a>
                    </li>
                    <li class="page-item disabled">
                        <span class="page-link text-dark">Página <?php echo (int)$currentPage; ?> de <?php echo (int)$totalPages; ?></span>
                    </li>
                    <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?controller=empresa&action=pipeline&page=<?php echo $currentPage + 1; ?>">Siguiente</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
<?php endif; ?>

<?php // Footer included from BaseController 
?>