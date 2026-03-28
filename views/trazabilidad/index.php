<?php // Layout included from BaseController 
?>

<?php
$etapaColors = [
    'prospectado' => 'badge-info',
    'contactado'  => 'badge-warning',
    'negociacion' => 'badge-primary',
    'ganado'      => 'badge-success',
    'perdido'     => 'badge-danger',
];
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800">
            <i class="fas fa-history text-primary mr-2"></i> Trazabilidad
        </h1>
        <?php if (isset($empresa)): ?>
            <p class="mb-0 text-gray-600">
                <strong><?php echo htmlspecialchars($empresa->razon_social); ?></strong>
                <span class="mx-2">-</span>
                <span class="badge badge-pill <?= $etapaColors[$empresa->etapa_venta] ?? 'badge-secondary' ?>">
                    <?= ucfirst($empresa->etapa_venta) ?>
                </span>
            </p>
        <?php endif; ?>
    </div>
    <div>
        <button type="button" class="btn btn-sm btn-success shadow-sm mr-2" data-toggle="modal" data-target="#modalExportarEmpresa">
            <i class="fas fa-file-csv fa-sm text-white-50 mr-1"></i> Exportar CSV
        </button>
        <a href="<?php echo url('trazabilidad/registrar', ['empresa_id' => $empresa_id]); ?>" class="btn btn-sm btn-primary shadow-sm mr-2">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Nueva Actividad
        </a>
        <a href="<?php echo url('empresa/index'); ?>" class="btn btn-sm btn-outline-secondary shadow-sm">
            <i class="fas fa-building fa-sm mr-1"></i> Empresas
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-stream mr-2"></i> Historial de Actividades
        </h6>
    </div>
    <div class="card-body">
        <div class="timeline">
            <?php if (empty($historial)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <p class="text-gray-500 mb-0">No hay actividades registradas aun.</p>
                </div>
            <?php else: ?>
                <?php foreach ($historial as $h): ?>
                    <?php
                    $tipoRaw = strtolower(trim((string)($h->tipo_actividad ?? 'nota')));
                    $tipoNorm = [
                        'reunión' => 'reunion',
                        'estudio de necesidades' => 'estudio_necesidades',
                        'oferta de servicio' => 'oferta_servicio',
                        'oferta de servicios' => 'oferta_servicio',
                        'seguimiento de la oferta' => 'seguimiento_oferta',
                        'seguimiento de oferta' => 'seguimiento_oferta',
                        'seguimiento' => 'seguimiento_oferta',
                    ][$tipoRaw] ?? $tipoRaw;
                    $iconos = [
                        'correo'  => ['fas fa-envelope', 'bg-primary', 'primary'],
                        'llamada' => ['fas fa-phone', 'bg-success', 'success'],
                        'nota'    => ['fas fa-sticky-note', 'bg-warning', 'warning'],
                        'reunion' => ['fas fa-handshake', 'bg-info', 'info'],
                        'visita'  => ['fas fa-map-marker-alt', 'bg-danger', 'danger'],
                        'estudio_necesidades' => ['fas fa-search', 'bg-secondary', 'secondary'],
                        'oferta_servicio'     => ['fas fa-file-signature', 'bg-dark', 'dark'],
                        'seguimiento_oferta'  => ['fas fa-eye', 'bg-info', 'info'],
                    ];
                    $tiposLabel = [
                        'correo'  => 'Correo',
                        'llamada' => 'Llamada',
                        'nota'    => 'Nota',
                        'reunion' => 'Reunion',
                        'visita'  => 'Visita',
                        'estudio_necesidades' => 'Estudio de Necesidades',
                        'oferta_servicio'     => 'Oferta de Servicio',
                        'seguimiento_oferta'  => 'Seguimiento de la Oferta',
                    ];
                    $meta = $iconos[$tipoNorm] ?? ['fas fa-circle', 'bg-secondary', 'secondary'];
                    $tipoLabel = $tiposLabel[$tipoNorm] ?? ucwords(str_replace('_', ' ', $tipoNorm));
                    $etapaBadge = $etapaColors[$h->etapa_venta] ?? 'badge-secondary';
                    ?>
                    <div class="timeline-item mb-3">
                        <div class="d-flex align-items-start">
                            <div class="timeline-icon <?= $meta[1] ?> rounded-circle d-flex align-items-center justify-content-center mr-3 flex-shrink-0" style="width:42px;height:42px;">
                                <i class="<?= $meta[0] ?> text-white fa-sm"></i>
                            </div>

                            <div class="card border-left-<?= $meta[2] ?> shadow-sm flex-grow-1">
                                <div class="card-body py-2 px-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="font-weight-bold text-primary"><?php echo htmlspecialchars($tipoLabel); ?></span>
                                            <i class="fas fa-arrow-right fa-xs text-gray-400 mx-2"></i>
                                            <span class="badge badge-pill <?= $etapaBadge ?>"><?php echo htmlspecialchars(ucfirst((string)$h->etapa_venta)); ?></span>
                                        </div>
                                        <small class="text-gray-500">
                                            <i class="fas fa-clock fa-xs mr-1"></i>
                                            <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($h->fecha))); ?>
                                        </small>
                                    </div>

                                    <?php if (!empty($h->observaciones)): ?>
                                        <p class="mb-1 mt-2 text-gray-700 small"><?php echo nl2br(htmlspecialchars($h->observaciones)); ?></p>
                                    <?php endif; ?>

                                    <small class="text-gray-500">
                                        <i class="fas fa-user fa-xs mr-1"></i>
                                        <?php echo htmlspecialchars($h->usuario); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Exportar Empresa -->
<div class="modal fade" id="modalExportarEmpresa" tabindex="-1" role="dialog" aria-labelledby="modalExportarEmpresaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalExportarEmpresaLabel"><i class="fas fa-file-csv mr-1"></i> Exportar Trazabilidad</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formExportarEmpresa" method="GET" action="<?php echo url('trazabilidad/exportar'); ?>">
                    <input type="hidden" name="empresa_id" value="<?php echo $empresa_id; ?>">

                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle"></i> Se exportará la trazabilidad de <strong><?php echo htmlspecialchars($empresa->razon_social ?? ''); ?></strong>
                    </div>

                    <div class="mb-2">
                        <label class="small font-weight-bold text-gray-700">Filtros Opcionales</label>
                        <p class="text-muted small">Deja los campos vacíos para exportar todo el historial</p>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_inicio_empresa" class="small font-weight-bold text-gray-700">Fecha Inicio</label>
                        <input type="date" class="form-control form-control-sm" id="fecha_inicio_empresa" name="fecha_inicio">
                    </div>

                    <div class="mb-3">
                        <label for="fecha_fin_empresa" class="small font-weight-bold text-gray-700">Fecha Fin</label>
                        <input type="date" class="form-control form-control-sm" id="fecha_fin_empresa" name="fecha_fin">
                    </div>

                    <div class="mb-0">
                        <label for="tipo_actividad_empresa" class="small font-weight-bold text-gray-700">Tipo de Actividad</label>
                        <select class="form-control form-control-sm" id="tipo_actividad_empresa" name="tipo_actividad">
                            <option value="">-- Todas --</option>
                            <option value="llamada">Llamada</option>
                            <option value="correo">Correo</option>
                            <option value="reunion">Reunión</option>
                            <option value="visita">Visita</option>
                            <option value="estudio_necesidades">Estudio de Necesidades</option>
                            <option value="oferta_servicio">Oferta de Servicio</option>
                            <option value="seguimiento_oferta">Seguimiento de la Oferta</option>
                            <option value="nota">Nota</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a href="<?php echo url('trazabilidad/exportar', ['empresa_id' => $empresa_id]); ?>" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-file-csv fa-sm mr-1"></i> Exportar Todo
                </a>
                <button type="button" class="btn btn-success btn-sm" onclick="document.getElementById('formExportarEmpresa').submit();">
                    <i class="fas fa-filter fa-sm mr-1"></i> Exportar con Filtros
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline-icon {
        min-width: 42px;
    }

    .timeline-item+.timeline-item {
        border-top: 1px solid #e3e6f0;
        padding-top: 1rem;
    }
</style>

<?php // Footer included from BaseController 
?>