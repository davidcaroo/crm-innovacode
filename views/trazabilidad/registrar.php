<?php // Layout included from BaseController 
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800">
            <i class="fas fa-calendar-check text-primary mr-2"></i> Registrar Actividad
        </h1>
        <?php if (isset($empresa)): ?>
            <p class="mb-0 text-gray-500 small font-weight-bold"><?php echo htmlspecialchars($empresa->razon_social); ?></p>
        <?php endif; ?>
    </div>
    <a href="<?php echo url('trazabilidad/index', ['empresa_id' => $empresa_id]); ?>" class="btn btn-sm btn-outline-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm mr-1"></i> Volver
    </a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-plus-circle mr-2"></i> Nueva Actividad
                </h6>
            </div>
            <div class="card-body">
                <form method="post" action="<?php echo url('trazabilidad/registrar', ['empresa_id' => $empresa_id]); ?>">
                    <input type="hidden" name="empresa_id" value="<?= htmlspecialchars($empresa_id) ?>">

                    <div class="form-group">
                        <label class="small font-weight-bold text-gray-700">Tipo de Actividad</label>
                        <select id="tipo_actividad" name="tipo_actividad" class="form-control form-control-sm" required>
                            <option value="llamada">Llamada</option>
                            <option value="correo">Correo</option>
                            <option value="reunion">Reunion</option>
                            <option value="visita">Visita</option>
                            <option value="estudio_necesidades">Estudio de Necesidades</option>
                            <option value="oferta_servicio">Oferta de Servicio</option>
                            <option value="nota" selected>Nota interna</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="small font-weight-bold text-gray-700">Nueva Etapa</label>
                        <select id="etapa_venta" name="etapa_venta" class="form-control form-control-sm" required>
                            <?php
                            $etapas = ['prospectado' => 'Prospectado', 'contactado' => 'Contactado', 'negociacion' => 'Negociacion', 'seguimiento' => 'Seguimiento', 'ganado' => 'Ganado', 'perdido' => 'Perdido'];
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
                        <label class="small font-weight-bold text-gray-700">Observaciones</label>
                        <textarea name="observaciones" class="form-control form-control-sm" rows="4" placeholder="Detalla la actividad..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save fa-sm text-white-50 mr-1"></i> Guardar Actividad
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tipoActividad = document.getElementById('tipo_actividad');
        var etapaVenta = document.getElementById('etapa_venta');

        if (!tipoActividad || !etapaVenta) {
            return;
        }

        tipoActividad.addEventListener('change', function() {
            if ((tipoActividad.value || '').toLowerCase() === 'oferta_servicio') {
                etapaVenta.value = 'negociacion';
            }
        });
    });
</script>

<?php // Footer included from BaseController 
?>