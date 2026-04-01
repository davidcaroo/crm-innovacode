<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-clock text-primary mr-2"></i> Recordatorios Programados</h1>
        <p class="mb-0 text-muted small">Revisa lo que el sistema tiene agendado para enviar por correo.</p>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h6 class="m-0 font-weight-bold text-primary">Listado de recordatorios</h6>
        <div class="btn-group btn-group-sm" role="group" aria-label="Filtros de estado">
            <a href="<?php echo url('trazabilidad/recordatorios'); ?>" class="btn btn-outline-secondary <?php echo empty($filtros['estado']) ? 'active' : ''; ?>">Todos</a>
            <a href="<?php echo url('trazabilidad/recordatorios', ['estado' => 'pendiente']); ?>" class="btn btn-outline-warning <?php echo ($filtros['estado'] ?? '') === 'pendiente' ? 'active' : ''; ?>">Pendientes</a>
            <a href="<?php echo url('trazabilidad/recordatorios', ['estado' => 'enviado']); ?>" class="btn btn-outline-success <?php echo ($filtros['estado'] ?? '') === 'enviado' ? 'active' : ''; ?>">Enviados</a>
            <a href="<?php echo url('trazabilidad/recordatorios', ['estado' => 'cancelado']); ?>" class="btn btn-outline-secondary <?php echo ($filtros['estado'] ?? '') === 'cancelado' ? 'active' : ''; ?>">Cancelados</a>
            <a href="<?php echo url('trazabilidad/recordatorios', ['estado' => 'fallido']); ?>" class="btn btn-outline-danger <?php echo ($filtros['estado'] ?? '') === 'fallido' ? 'active' : ''; ?>">Fallidos</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Empresa</th>
                        <th>Responsable</th>
                        <th>Tipo</th>
                        <th>Asunto</th>
                        <th>Programado para</th>
                        <th>Estado</th>
                        <th>Ejecutado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recordatorios)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No hay recordatorios para mostrar.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recordatorios as $recordatorio): ?>
                            <?php
                                $estado = strtolower((string)($recordatorio->estado ?? ''));
                                $badge = [
                                    'pendiente' => 'warning',
                                    'enviado' => 'success',
                                    'cancelado' => 'secondary',
                                    'fallido' => 'danger',
                                ][$estado] ?? 'secondary';
                                $tipo = str_replace('_', ' ', (string)($recordatorio->tipo_recordatorio ?? ''));
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($recordatorio->razon_social ?? ''); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($recordatorio->correo_comercial ?? ''); ?></small>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($recordatorio->usuario_nombre ?? ''); ?><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($recordatorio->usuario_email ?? ''); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars(ucwords($tipo)); ?></td>
                                <td><?php echo htmlspecialchars($recordatorio->asunto ?? ''); ?></td>
                                <td><?php echo !empty($recordatorio->fecha_programada) ? date('d/m/Y H:i', strtotime($recordatorio->fecha_programada)) : '-'; ?></td>
                                <td><span class="badge badge-<?php echo $badge; ?>"><?php echo htmlspecialchars(ucfirst($estado)); ?></span></td>
                                <td>
                                    <?php echo !empty($recordatorio->fecha_ejecucion) ? date('d/m/Y H:i', strtotime($recordatorio->fecha_ejecucion)) : '-'; ?><br>
                                    <?php if (!empty($recordatorio->error_msg)): ?>
                                        <small class="text-danger"><?php echo htmlspecialchars($recordatorio->error_msg); ?></small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>