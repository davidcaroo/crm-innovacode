<?php
// views/trazabilidad/global.php
?>
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800"><i class="mdi mdi-history mr-2" style="color: #28a745; font-weight: bold;"></i>Historial de Actividad Global</h2>
        <div class="d-flex" style="gap:8px;">
            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalExportar">
                <i class="bi bi-download"></i> Exportar a CSV
            </button>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="tablaActividad">
                    <thead class="thead-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Empresa</th>
                            <th>Vendedor</th>
                            <th>Actividad</th>
                            <th>Etapa</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historial as $h): ?>
                            <tr>
                                <td class="small"><?= date('d/m/Y H:i', strtotime($h->fecha)) ?></td>
                                <td>
                                    <a href="<?php echo url('trazabilidad/index', ['empresa_id' => $h->empresa_id]); ?>" class="font-weight-bold">
                                        <?= htmlspecialchars($h->empresa) ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-light p-2 text-dark">
                                        <i class="mdi mdi-account mr-1"></i><?= htmlspecialchars($h->usuario) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $icon = 'mdi-message-text';
                                    if ($h->tipo_actividad == 'llamada') $icon = 'mdi-phone';
                                    if ($h->tipo_actividad == 'correo') $icon = 'mdi-email';
                                    if ($h->tipo_actividad == 'reunion') $icon = 'mdi-account-group';
                                    ?>
                                    <i class="mdi <?= $icon ?> mr-1 text-info"></i>
                                    <?= ucfirst($h->tipo_actividad) ?>
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
                                    <span class="badge <?= $badgeClass ?>"><?= ucfirst($h->etapa_venta) ?></span>
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
</div>

<!-- Modal Exportar -->
<div class="modal fade" id="modalExportar" tabindex="-1" role="dialog" aria-labelledby="modalExportarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalExportarLabel"><i class="bi bi-download"></i> Exportar Trazabilidad</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formExportar" method="GET" action="<?php echo url('trazabilidad/exportar'); ?>">
                    <div class="form-group">
                        <label class="font-weight-bold">Filtros Opcionales</label>
                        <p class="text-muted small">Deja los campos vacíos para exportar todo el historial</p>
                    </div>

                    <div class="form-group">
                        <label for="fecha_inicio">Fecha Inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                    </div>

                    <div class="form-group">
                        <label for="fecha_fin">Fecha Fin</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                    </div>

                    <div class="form-group">
                        <label for="tipo_actividad">Tipo de Actividad</label>
                        <select class="form-control" id="tipo_actividad" name="tipo_actividad">
                            <option value="">-- Todas --</option>
                            <option value="llamada">Llamada</option>
                            <option value="correo">Correo</option>
                            <option value="reunion">Reunión</option>
                            <option value="visita">Visita</option>
                            <option value="nota">Nota</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a href="<?php echo url('trazabilidad/exportar'); ?>" class="btn btn-outline-success">
                    <i class="bi bi-file-earmark-arrow-down"></i> Exportar Todo
                </a>
                <button type="button" class="btn btn-success" onclick="document.getElementById('formExportar').submit();">
                    <i class="bi bi-funnel"></i> Exportar con Filtros
                </button>
            </div>
        </div>
    </div>
</div>