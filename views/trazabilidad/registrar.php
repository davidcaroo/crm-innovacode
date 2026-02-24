<?php // Layout included from BaseController 
?>

<div class="page-header">
    <div>
        <h2 class="page-title"><span class="mdi mdi-timeline-plus" style="color: #28a745;"></span> Registrar Actividad</h2>
        <?php if (isset($empresa)): ?>
            <small class="text-muted"><?php echo htmlspecialchars($empresa->razon_social); ?></small>
        <?php endif; ?>
    </div>
    <a href="<?php echo url('trazabilidad/index', ['empresa_id' => $empresa_id]); ?>" class="btn btn-sm btn-outline-secondary">
        <span class="mdi mdi-arrow-left"></span> Volver
    </a>
</div>

<div class="row">
    <div class="col-12 col-md-7 col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="post" action="<?php echo url('trazabilidad/registrar', ['empresa_id' => $empresa_id]); ?>">
                    <input type="hidden" name="empresa_id" value="<?= htmlspecialchars($empresa_id) ?>">

                    <div class="form-group">
                        <label><small class="font-weight-bold text-uppercase text-muted">Tipo de Actividad</small></label>
                        <select name="tipo_actividad" class="form-control" required style="color:#2d3a4a;background-color:#fff;">
                            <option value="llamada"><span class="mdi mdi-phone"></span> Llamada</option>
                            <option value="correo">Correo</option>
                            <option value="reunion">Reunion</option>
                            <option value="visita">Visita</option>
                            <option value="nota" selected>Nota interna</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><small class="font-weight-bold text-uppercase text-muted">Nueva Etapa</small></label>
                        <select name="etapa_venta" class="form-control" required style="color:#2d3a4a;background-color:#fff;">
                            <?php
                            $etapas = ['prospectado' => 'Prospectado', 'contactado' => 'Contactado', 'negociacion' => 'Negociacion', 'ganado' => 'Ganado', 'perdido' => 'Perdido'];
                            $etapaActual = isset($empresa) ? $empresa->etapa_venta : '';
                            foreach ($etapas as $val => $label):
                            ?>
                                <option value="<?= $val ?>" <?= $etapaActual === $val ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><small class="font-weight-bold text-uppercase text-muted">Observaciones</small></label>
                        <textarea name="observaciones" class="form-control" rows="4" placeholder="Detalla la actividad..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <span class="mdi mdi-content-save"></span> Guardar Actividad
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php // Footer included from BaseController 
?>