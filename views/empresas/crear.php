<?php // Layout included from BaseController 
?>

<div class="page-header">
    <div>
        <h2 class="page-title"><i class="bi bi-building-add"></i> Nueva Empresa</h2>
        <span class="page-subtitle">Completa los datos para registrar una empresa</span>
    </div>
    <a href="<?php echo BASE_URL; ?>/index.php?controller=empresa&action=index"
        class="btn btn-sm btn-danger text-white" style="border-radius:6px; font-weight:800;">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<form method="post" action="<?php echo BASE_URL; ?>/index.php?controller=empresa&action=guardar">

    <div class="row">

        <!-- Columna izquierda -->
        <div class="col-12 col-md-6 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <p style="font-size:0.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.6px;border-bottom:1px solid #e4e8f0;padding-bottom:8px;margin-bottom:16px;">
                        <i class="bi bi-info-circle"></i> Información General
                    </p>

                    <div class="form-group mb-3">
                        <label style="font-size:0.82rem;font-weight:600;color:#475569;">Razón Social <span class="text-danger">*</span></label>
                        <input type="text" name="razon_social" class="form-control form-control-sm"
                            required placeholder="Nombre de la empresa" autofocus>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label style="font-size:0.82rem;font-weight:600;color:#475569;">Departamento</label>
                                <input type="text" name="dpto" class="form-control form-control-sm" placeholder="Ej. Atlántico">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label style="font-size:0.82rem;font-weight:600;color:#475569;">Ciudad</label>
                                <input type="text" name="ciudad" class="form-control form-control-sm" placeholder="Ej. Barranquilla">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label style="font-size:0.82rem;font-weight:600;color:#475569;">Actividad Económica</label>
                        <input type="text" name="actividad_economica" class="form-control form-control-sm"
                            placeholder="Ej. Transporte, Manufactura">
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna derecha -->
        <div class="col-12 col-md-6 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <p style="font-size:0.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.6px;border-bottom:1px solid #e4e8f0;padding-bottom:8px;margin-bottom:16px;">
                        <i class="bi bi-graph-up-arrow"></i> Datos Comerciales
                    </p>

                    <div class="form-group mb-3">
                        <label style="font-size:0.82rem;font-weight:600;color:#475569;">Correo Comercial</label>
                        <input type="email" name="correo_comercial" class="form-control form-control-sm"
                            placeholder="correo@empresa.com">
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label style="font-size:0.82rem;font-weight:600;color:#475569;">Etapa de Venta</label>
                                <select name="etapa_venta" class="form-control form-control-sm">
                                    <option value="prospectado" selected>Prospectado</option>
                                    <option value="contactado">Contactado</option>
                                    <option value="negociacion">Negociación</option>
                                    <option value="ganado">Ganado</option>
                                    <option value="perdido">Perdido</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label style="font-size:0.82rem;font-weight:600;color:#475569;">¿Aplica?</label>
                                <select name="aplica" class="form-control form-control-sm">
                                    <option value="SI">Sí</option>
                                    <option value="NO">No</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label style="font-size:0.82rem;font-weight:600;color:#475569;">Observaciones</label>
                        <textarea name="observaciones" class="form-control form-control-sm" rows="4"
                            placeholder="Notas internas sobre la empresa..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-4" style="gap:10px;">
        <a href="<?php echo BASE_URL; ?>/index.php?controller=empresa&action=index"
            class="btn btn-danger text-white" style="border-radius:8px;padding:8px 22px; font-weight:800;">Cancelar</a>
        <button type="submit" class="btn btn-primary" style="border-radius:8px;padding:8px 28px;font-weight:600;">
            <i class="bi bi-plus-lg"></i> Registrar Empresa
        </button>
    </div>
</form>

<?php // Footer included from BaseController 
?>