<?php // Layout included from BaseController 
?>

<div class="page-header">
    <h2 class="page-title"><span class="mdi mdi-account-plus"></span> Nuevo Usuario</h2>
    <a href="<?php echo url('usuario/lista'); ?>" class="btn btn-sm btn-outline-secondary">
        <span class="mdi mdi-arrow-left"></span> Volver
    </a>
</div>

<div class="row">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="post" action="<?php echo url('usuario/guardarUsuario'); ?>">
                    <div class="form-group">
                        <label><small class="font-weight-bold text-uppercase text-muted">Nombre</small></label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label><small class="font-weight-bold text-uppercase text-muted">Email</small></label>
                        <input type="email" name="email" class="form-control" required>
                        <small class="form-text text-muted">
                            <i class="mdi mdi-information"></i> Se enviará un correo con las credenciales de acceso a esta dirección.
                        </small>
                    </div>

                    <!-- Información sobre contraseña automática -->
                    <div class="alert alert-info" style="background-color:#e0f2fe;border-left:4px solid #3b82f6;border-radius:8px;padding:12px;">
                        <i class="mdi mdi-lock-reset" style="color:#1e40af;"></i>
                        <strong>Contraseña automática:</strong><br>
                        <small style="color:#1e3a8a;">
                            Se generará una contraseña temporal segura y se enviará por correo electrónico.
                            El usuario deberá cambiarla al iniciar sesión por primera vez.
                        </small>
                    </div>

                    <div class="form-group">
                        <label><small class="font-weight-bold text-uppercase text-muted">Rol</small></label>
                        <select name="rol" class="form-control">
                            <option value="usuario">Usuario</option>
                            <option value="admin">Admin</option>
                            <option value="superadmin">SuperAdmin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block mt-2">
                        <span class="mdi mdi-content-save"></span> Crear Usuario
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php // Footer included from BaseController 
?>