<?php
// views/trazabilidad/global.php
?>
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800"><i class="mdi mdi-history mr-2 text-primary"></i>Historial de Actividad Global</h2>
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
                                    $badgeClass = 'bg-secondary';
                                    if ($h->etapa_venta == 'ganado') $badgeClass = 'bg-success';
                                    if ($h->etapa_venta == 'perdido') $badgeClass = 'bg-danger';
                                    if ($h->etapa_venta == 'negociacion') $badgeClass = 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?= $badgeClass ?> text-white"><?= ucfirst($h->etapa_venta) ?></span>
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