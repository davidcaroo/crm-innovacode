<?php // Layout included from BaseController 
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800">
            <i class="fas fa-edit text-primary mr-2"></i> Editar Empresa
        </h1>
        <p class="mb-0 text-gray-500 small font-weight-bold"><?= htmlspecialchars($empresa->razon_social) ?></p>
    </div>
    <a href="<?php echo url('empresa/index'); ?>" class="btn btn-sm btn-outline-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm mr-1"></i> Volver
    </a>
</div>

<form method="post" action="<?php echo url('empresa/actualizar'); ?>">
    <input type="hidden" name="id" value="<?= htmlspecialchars($empresa->id) ?>">

    <div class="row">

        <!-- Columna izquierda -->
        <div class="col-lg-6">
            <div class="card shadow mb-4 h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle mr-2"></i> Información General
                    </h6>
                </div>
                <div class="card-body">

                    <div class="form-group">
                        <label class="small font-weight-bold text-gray-700">Razón Social <span class="text-danger">*</span></label>
                        <input type="text" name="razon_social" class="form-control form-control-sm"
                            value="<?= htmlspecialchars($empresa->razon_social) ?>" required
                            placeholder="Nombre de la empresa">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="small font-weight-bold text-gray-700">Departamento</label>
                            <input type="text" name="dpto" class="form-control form-control-sm"
                                value="<?= htmlspecialchars($empresa->dpto) ?>" placeholder="Ej. Atlántico">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="small font-weight-bold text-gray-700">Ciudad</label>
                            <input type="text" name="ciudad" class="form-control form-control-sm"
                                value="<?= htmlspecialchars($empresa->ciudad) ?>" placeholder="Ej. Barranquilla">
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-gray-700">Actividad Económica</label>
                        <input type="text" name="actividad_economica" class="form-control form-control-sm"
                            value="<?= htmlspecialchars($empresa->actividad_economica) ?>"
                            placeholder="Ej. Transporte, Manufactura">
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna derecha -->
        <div class="col-lg-6">
            <div class="card shadow mb-4 h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-handshake mr-2"></i> Datos Comerciales
                    </h6>
                </div>
                <div class="card-body">

                    <div class="form-group">
                        <label class="small font-weight-bold text-gray-700">Correo Comercial</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            </div>
                            <input type="email" name="correo_comercial" class="form-control form-control-sm"
                                value="<?= htmlspecialchars($empresa->correo_comercial) ?>"
                                placeholder="correo@empresa.com">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-7">
                            <label class="small font-weight-bold text-gray-700">Etapa de Venta</label>
                            <select id="etapa_venta" name="etapa_venta" class="form-control form-control-sm">
                                <option value="prospectado" <?= (!isset($empresa->etapa_venta) || $empresa->etapa_venta === 'prospectado') ? 'selected' : '' ?>>Prospectado</option>
                                <option value="contactado" <?= (isset($empresa->etapa_venta) && $empresa->etapa_venta === 'contactado')  ? 'selected' : '' ?>>Contactado</option>
                                <option value="negociacion" <?= (isset($empresa->etapa_venta) && $empresa->etapa_venta === 'negociacion') ? 'selected' : '' ?>>Negociación</option>
                                <option value="ganado" <?= (isset($empresa->etapa_venta) && $empresa->etapa_venta === 'ganado')      ? 'selected' : '' ?>>Ganado</option>
                                <option value="perdido" <?= (isset($empresa->etapa_venta) && $empresa->etapa_venta === 'perdido')     ? 'selected' : '' ?>>Perdido</option>
                            </select>
                        </div>
                        <div class="form-group col-md-5">
                            <label class="small font-weight-bold text-gray-700">¿Aplica?</label>
                            <select id="aplica" name="aplica" class="form-control form-control-sm">
                                <option value="SI" <?= (!isset($empresa->aplica) || $empresa->aplica === 'SI') ? 'selected' : '' ?>>Sí</option>
                                <option value="NO" <?= (isset($empresa->aplica) && $empresa->aplica === 'NO') ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-gray-700">Observaciones</label>
                        <textarea name="observaciones" class="form-control form-control-sm" rows="4"
                            placeholder="Notas internas sobre la empresa..."><?= htmlspecialchars($empresa->observaciones ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-end">
                        <a href="<?php echo url('empresa/index'); ?>" class="btn btn-danger mr-2">
                            <i class="fas fa-times fa-sm text-white-50 mr-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save fa-sm text-white-50 mr-1"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var aplicaSelect = document.getElementById('aplica');
        var etapaSelect = document.getElementById('etapa_venta');

        if (!aplicaSelect || !etapaSelect) {
            return;
        }

        function sincronizarEtapaConAplica() {
            var aplica = (aplicaSelect.value || '').toUpperCase();
            if (aplica === 'NO') {
                etapaSelect.value = 'perdido';
                etapaSelect.setAttribute('disabled', 'disabled');
            } else {
                etapaSelect.removeAttribute('disabled');
            }
        }

        aplicaSelect.addEventListener('change', sincronizarEtapaConAplica);
        sincronizarEtapaConAplica();
    });
</script>

<?php // Footer included from BaseController 
?>