<?php
// Layout incluido desde BaseController::view()
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800"><i class="fas fa-cash-register text-primary mr-2"></i>Ventas</h1>
        <p class="mb-0 small text-muted">
            <?php echo count($ventas); ?> venta<?php echo count($ventas) != 1 ? 's' : ''; ?> registrada<?php echo count($ventas) != 1 ? 's' : ''; ?>
            <?php if ($totalVentas > 0): ?>
                &nbsp;-&nbsp;<span class="text-success font-weight-bold">Total: $<?php echo number_format($totalVentas, 2); ?></span>
            <?php endif; ?>
        </p>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-5 mb-4">
        <div class="card shadow mb-4 h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-plus-circle mr-2"></i>Registrar Venta</h6>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo url('venta/guardar'); ?>" method="post">
                    <div class="form-group">
                        <label class="small font-weight-bold text-gray-700">Empresa (Ganada)</label>
                        <select required name="empresa_id" class="form-control form-control-sm">
                            <option value="">-- Seleccione empresa --</option>
                            <?php foreach ($empresasGanadas as $emp): ?>
                                <option value="<?php echo $emp->id; ?>">
                                    <?php echo htmlspecialchars($emp->razon_social); ?> - <?php echo htmlspecialchars($emp->dpto ?? ''); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (empty($empresasGanadas)): ?>
                            <small class="d-block mt-1 text-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                No hay empresas en etapa <strong>Ganado</strong>.
                                <a href="<?php echo url('empresa/index'); ?>">Editar empresas</a>
                            </small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="small font-weight-bold text-gray-700">Monto</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input required type="number" step="0.01" min="0"
                                class="form-control form-control-sm" placeholder="0.00" name="monto">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="small font-weight-bold text-gray-700">Fecha</label>
                        <input required type="date" value="<?php echo date('Y-m-d'); ?>"
                            class="form-control form-control-sm" name="fecha">
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-save fa-sm text-white-50 mr-1"></i> Registrar Venta
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-7 mb-4">
        <div class="card shadow mb-4 h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i>Historial de Ventas</h6>
            </div>
            <div class="card-body p-0">
                <?php if (empty($ventas)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-shopping-cart fa-2x d-block mb-2 text-secondary"></i>
                        No hay ventas registradas aun.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Empresa</th>
                                    <th>Dpto</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                    <th class="text-right pr-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ventas as $v): ?>
                                    <tr>
                                        <td class="font-weight-bold"><?php echo htmlspecialchars($v->empresa_nombre); ?></td>
                                        <td><small class="text-muted"><?php echo htmlspecialchars($v->departamento ?? '-'); ?></small></td>
                                        <td class="font-weight-bold text-success">$<?php echo number_format($v->monto, 2); ?></td>
                                        <td><small class="text-muted"><?php echo htmlspecialchars($v->fecha); ?></small></td>
                                        <td class="text-right pr-3">
                                            <a href="#"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirmarEliminacion('<?php echo url('venta/eliminar', ['id' => $v->id]); ?>', '¿Eliminar esta venta por $<?php echo number_format($v->monto, 2); ?>?')"
                                                title="Eliminar venta">
                                                <i class="fas fa-trash-alt fa-sm"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Footer incluido desde BaseController::view()
?>