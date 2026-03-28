<?php // Layout included from BaseController 
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800">
            <i class="fas fa-building text-primary mr-2"></i> Nueva Empresa
        </h1>
        <p class="mb-0 text-gray-500 small font-weight-bold">Completa los datos para registrar una empresa</p>
    </div>
    <a href="<?php echo url('empresa/index'); ?>" class="btn btn-sm btn-outline-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm mr-1"></i> Volver
    </a>
</div>

<?php if (isset($_GET['error']) && $_GET['error'] === 'aplica_required'): ?>
    <div class="alert alert-warning shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle mr-1"></i>
        Debes seleccionar si la empresa aplica o no antes de guardar.
    </div>
<?php endif; ?>

<form method="post" action="<?php echo url('empresa/guardar'); ?>">

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
                            required placeholder="Nombre de la empresa" autofocus>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="small font-weight-bold text-gray-700">Departamento</label>
                            <input type="text" name="dpto" class="form-control form-control-sm" placeholder="Ej. Atlántico">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="small font-weight-bold text-gray-700">Ciudad</label>
                            <input type="text" name="ciudad" class="form-control form-control-sm" placeholder="Ej. Barranquilla">
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-gray-700">Actividad Económica</label>
                        <input type="text" name="actividad_economica" class="form-control form-control-sm"
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
                                placeholder="correo@empresa.com">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-7">
                            <label class="small font-weight-bold text-gray-700">Etapa de Venta</label>
                            <select id="etapa_venta" name="etapa_venta" class="form-control form-control-sm">
                                <option value="prospectado" selected>Prospectado</option>
                                <option value="contactado">Contactado</option>
                                <option value="negociacion">Negociación</option>
                                <option value="ganado">Ganado</option>
                                <option value="perdido">Perdido</option>
                            </select>
                        </div>
                        <div class="form-group col-md-5">
                            <label class="small font-weight-bold text-gray-700">¿Aplica?</label>
                            <select id="aplica" name="aplica" class="form-control form-control-sm" required>
                                <option value="" selected disabled>Seleccionar...</option>
                                <option value="SI">Sí</option>
                                <option value="NO">No</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-gray-700">Observaciones</label>
                        <textarea name="observaciones" class="form-control form-control-sm" rows="4"
                            placeholder="Notas internas sobre la empresa..."></textarea>
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
                            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Registrar Empresa
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
            var etapaActual = (etapaSelect.value || '').toLowerCase();
            var etapaAvanzada = (etapaActual === 'negociacion' || etapaActual === 'ganado');

            if (aplica === 'NO') {
                etapaSelect.value = 'perdido';
                etapaSelect.setAttribute('disabled', 'disabled');
            } else if (aplica === 'SI') {
                if (!etapaAvanzada) {
                    etapaSelect.value = 'contactado';
                }
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