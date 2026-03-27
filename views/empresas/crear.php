<?php // Layout included from BaseController 
?>

<div class="page-header">
    <div>
        <h2 class="page-title"><i class="fa-solid fa-building-circle-check"></i> Nueva Empresa</h2>
        <span class="page-subtitle">Completa los datos para registrar una empresa</span>
    </div>
    <a href="<?php echo url('empresa/index'); ?>"
        class="btn btn-sm btn-danger text-white fw-bold">
        <i class="fa-solid fa-arrow-left"></i> Volver
    </a>
</div>

<form method="post" action="<?php echo url('empresa/guardar'); ?>">

    <div class="row">

        <!-- Columna izquierda -->
        <div class="col-12 col-md-6 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <p class="crm-section-title">
                        <i class="fa-solid fa-circle-info"></i> Información General
                    </p>

                    <div class="mb-3">
                        <label class="crm-form-label">Razón Social <span class="text-danger">*</span></label>
                        <input type="text" name="razon_social" class="form-control"
                            required placeholder="Nombre de la empresa" autofocus>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="crm-form-label">Departamento</label>
                                <input type="text" name="dpto" class="form-control" placeholder="Ej. Atlántico">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="crm-form-label">Ciudad</label>
                                <input type="text" name="ciudad" class="form-control" placeholder="Ej. Barranquilla">
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="crm-form-label">Actividad Económica</label>
                        <input type="text" name="actividad_economica" class="form-control"
                            placeholder="Ej. Transporte, Manufactura">
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna derecha -->
        <div class="col-12 col-md-6 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <p class="crm-section-title">
                        <i class="fa-solid fa-chart-line"></i> Datos Comerciales
                    </p>

                    <div class="mb-3">
                        <label class="crm-form-label">Correo Comercial</label>
                        <input type="email" name="correo_comercial" class="form-control"
                            placeholder="correo@empresa.com">
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="crm-form-label">Etapa de Venta</label>
                                <select name="etapa_venta" class="form-select crm-select">
                                    <option value="prospectado" selected>Prospectado</option>
                                    <option value="contactado">Contactado</option>
                                    <option value="negociacion">Negociación</option>
                                    <option value="ganado">Ganado</option>
                                    <option value="perdido">Perdido</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="crm-form-label">¿Aplica?</label>
                                <select name="aplica" class="form-select crm-select">
                                    <option value="SI">Sí</option>
                                    <option value="NO">No</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="crm-form-label">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="4"
                            placeholder="Notas internas sobre la empresa..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-4 gap-2">
        <a href="<?php echo url('empresa/index'); ?>" class="btn btn-danger text-white fw-bold">Cancelar</a>
        <button type="submit" class="btn btn-primary fw-semibold">
            <i class="fa-solid fa-plus"></i> Registrar Empresa
        </button>
    </div>
</form>

<?php // Footer included from BaseController 
?>