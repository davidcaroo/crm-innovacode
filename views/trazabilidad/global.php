<?php
// views/trazabilidad/global.php
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800">
            <i class="fas fa-history text-primary mr-2"></i> Historial de Actividad Global
        </h1>
        <p class="mb-0 text-gray-500 small">Seguimiento consolidado de interacciones por empresa y vendedor</p>
    </div>
    <button type="button" class="btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#modalExportar">
        <i class="fas fa-file-csv fa-sm text-white-50 mr-1"></i> Exportar a CSV
    </button>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-stream mr-2"></i> Actividad Consolidada
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tablaActividad" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th><i class="fas fa-clock fa-xs mr-1"></i> Fecha</th>
                        <th><i class="fas fa-building fa-xs mr-1"></i> Empresa</th>
                        <th><i class="fas fa-user fa-xs mr-1"></i> Comercial</th>
                        <th><i class="fas fa-clipboard-list fa-xs mr-1"></i> Actividad</th>
                        <th><i class="fas fa-flag fa-xs mr-1"></i> Etapa</th>
                        <th><i class="fas fa-comment-dots fa-xs mr-1"></i> Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historial as $h): ?>
                        <tr>
                            <td class="small"><?= date('d/m/Y H:i', strtotime($h->fecha)) ?></td>
                            <td>
                                <a href="<?php echo url('trazabilidad/index', ['empresa_id' => $h->empresa_id]); ?>" class="font-weight-bold text-decoration-none">
                                    <?= htmlspecialchars($h->empresa) ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge badge-pill badge-light text-dark border px-2 py-1">
                                    <i class="fas fa-user mr-1"></i><?= htmlspecialchars($h->usuario) ?>
                                </span>
                            </td>
                            <td>
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
                                $tipos = [
                                    'llamada' => ['fas fa-phone', 'Llamada'],
                                    'correo' => ['fas fa-envelope', 'Correo'],
                                    'reunion' => ['fas fa-handshake', 'Reunion'],
                                    'visita' => ['fas fa-map-marker-alt', 'Visita'],
                                    'estudio_necesidades' => ['fas fa-search', 'Estudio de Necesidades'],
                                    'oferta_servicio' => ['fas fa-file-signature', 'Oferta de Servicio'],
                                    'seguimiento_oferta' => ['fas fa-eye', 'Seguimiento de la Oferta'],
                                    'nota' => ['fas fa-sticky-note', 'Nota'],
                                ];
                                $metaTipo = $tipos[$tipoNorm] ?? ['fas fa-circle', ucwords(str_replace('_', ' ', $tipoNorm))];
                                ?>
                                <i class="<?= $metaTipo[0] ?> mr-1 text-info"></i>
                                <?= htmlspecialchars($metaTipo[1]) ?>
                            </td>
                            <td>
                                <?php
                                $badgeClass = 'badge-secondary';
                                if ($h->etapa_venta == 'ganado') $badgeClass = 'badge-success';
                                if ($h->etapa_venta == 'perdido') $badgeClass = 'badge-danger';
                                if ($h->etapa_venta == 'negociacion') $badgeClass = 'badge-warning';
                                if ($h->etapa_venta == 'prospectado') $badgeClass = 'badge-info';
                                if ($h->etapa_venta == 'contactado') $badgeClass = 'badge-primary';
                                ?>
                                <span class="badge badge-pill <?= $badgeClass ?>"><?= ucfirst($h->etapa_venta) ?></span>
                            </td>
                            <td class="small text-muted"><?= htmlspecialchars($h->observaciones) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($historial)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No hay actividad registrada aún.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Exportar -->
<div class="modal fade" id="modalExportar" tabindex="-1" role="dialog" aria-labelledby="modalExportarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalExportarLabel"><i class="fas fa-file-csv mr-1"></i> Exportar Trazabilidad</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formExportar" method="GET" action="<?php echo url('trazabilidad/exportar'); ?>">
                    <div class="mb-2">
                        <label class="small font-weight-bold text-gray-700">Filtros Opcionales</label>
                        <p class="text-muted small">Deja los campos vacíos para exportar todo el historial</p>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_inicio" class="small font-weight-bold text-gray-700">Fecha Inicio</label>
                        <input type="date" class="form-control form-control-sm" id="fecha_inicio" name="fecha_inicio">
                    </div>

                    <div class="mb-3">
                        <label for="fecha_fin" class="small font-weight-bold text-gray-700">Fecha Fin</label>
                        <input type="date" class="form-control form-control-sm" id="fecha_fin" name="fecha_fin">
                    </div>

                    <div class="mb-0">
                        <label for="tipo_actividad" class="small font-weight-bold text-gray-700">Tipo de Actividad</label>
                        <select class="form-control form-control-sm" id="tipo_actividad" name="tipo_actividad">
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
                <a href="<?php echo url('trazabilidad/exportar'); ?>" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-file-csv fa-sm mr-1"></i> Exportar Todo
                </a>
                <button type="button" class="btn btn-success btn-sm" onclick="document.getElementById('formExportar').submit();">
                    <i class="fas fa-filter fa-sm text-white-50 mr-1"></i> Exportar con Filtros
                </button>
            </div>
        </div>
    </div>
</div>