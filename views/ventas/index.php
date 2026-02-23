<?php
// Layout incluido desde BaseController::view()
?>

<div class="page-header">
    <div>
        <h2 class="page-title"><span class="mdi mdi-cash-multiple"></span> Ventas</h2>
        <span class="page-subtitle">
            <?php echo count($ventas); ?> venta<?php echo count($ventas) != 1 ? 's' : ''; ?> registrada<?php echo count($ventas) != 1 ? 's' : ''; ?>
            <?php if ($totalVentas > 0): ?>
                &nbsp;&mdash;&nbsp;<span style="color:#15803d;font-weight:700;">Total: $<?php echo number_format($totalVentas, 2); ?></span>
            <?php endif; ?>
        </span>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-5 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body" style="padding:20px;">
                <div class="d-flex align-items-center mb-3">
                    <span class="mdi mdi-plus-circle-outline" style="font-size:1.5rem;color:#1e40af;margin-right:8px;"></span>
                    <h5 class="mb-0" style="color:#1e40af;font-weight:700;">Registrar Venta</h5>
                </div>
                <form action="<?php echo BASE_URL; ?>/index.php?controller=venta&action=guardar" method="post">
                    <div class="form-group mb-2">
                        <label class="mb-1"><small class="font-weight-bold text-uppercase" style="color:#64748b;font-size:0.75rem;">Empresa (Ganada)</small></label>
                        <select required name="empresa_id" class="form-control form-control-sm">
                            <option value="">-- Seleccione empresa --</option>
                            <?php foreach ($empresasGanadas as $emp): ?>
                                <option value="<?php echo $emp->id; ?>">
                                    <?php echo htmlspecialchars($emp->razon_social); ?> - <?php echo htmlspecialchars($emp->dpto ?? ''); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (empty($empresasGanadas)): ?>
                            <small class="d-block mt-1" style="color:#f59e0b;">
                                <span class="mdi mdi-alert"></span>
                                No hay empresas en etapa <strong>Ganado</strong>.
                                <a href="<?php echo BASE_URL; ?>/index.php?controller=empresa&action=index">Editar empresas</a>
                            </small>
                        <?php endif; ?>
                    </div>
                    <div class="form-group mb-2">
                        <label class="mb-1"><small class="font-weight-bold text-uppercase" style="color:#64748b;font-size:0.75rem;">Monto</small></label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="background:#f8fafc;border-color:#e2e8f0;">$</span>
                            </div>
                            <input required type="number" step="0.01" min="0"
                                class="form-control form-control-sm" placeholder="0.00" name="monto">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="mb-1"><small class="font-weight-bold text-uppercase" style="color:#64748b;font-size:0.75rem;">Fecha</small></label>
                        <input required type="date" value="<?php echo date('Y-m-d'); ?>"
                            class="form-control form-control-sm" name="fecha">
                    </div>
                    <button type="submit" class="btn btn-success btn-block" style="padding:8px;font-weight:600;">
                        <span class="mdi mdi-content-save"></span> Registrar Venta
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-7 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="px-3 pt-3 pb-2 d-flex align-items-center" style="border-bottom:1px solid #e4e8f0;">
                    <span class="mdi mdi-history" style="font-size:1.3rem;color:#1e40af;margin-right:8px;"></span>
                    <h5 class="mb-0" style="color:#1e40af;font-weight:700;">Historial de Ventas</h5>
                </div>
                <?php if (empty($ventas)): ?>
                    <div class="text-center text-muted py-5">
                        <span class="mdi mdi-cart-off" style="font-size:2.5rem;display:block;margin-bottom:8px;color:#cbd5e1;"></span>
                        No hay ventas registradas aun.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Empresa</th>
                                    <th>Dpto</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ventas as $v): ?>
                                    <tr>
                                        <td class="font-weight-bold"><?php echo htmlspecialchars($v->empresa_nombre); ?></td>
                                        <td><small class="text-muted"><?php echo htmlspecialchars($v->departamento ?? '-'); ?></small></td>
                                        <td class="font-weight-bold" style="color:#15803d;">$<?php echo number_format($v->monto, 2); ?></td>
                                        <td><small class="text-muted"><?php echo htmlspecialchars($v->fecha); ?></small></td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>/index.php?controller=venta&action=eliminar&id=<?= $v->id ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('¿Eliminar esta venta?')"
                                                style="padding:3px 8px;font-size:0.8rem;border-radius:6px;">
                                                <span class="mdi mdi-delete"></span>
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