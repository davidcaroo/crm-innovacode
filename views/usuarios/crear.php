<?php // Layout included from BaseController 
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800"><i class="fas fa-user-plus text-primary mr-2"></i> Nuevo Usuario</h1>
    </div>
    <a href="<?php echo url('usuario/lista'); ?>" class="btn btn-sm btn-outline-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm mr-1"></i> Volver
    </a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-plus mr-2"></i> Registro de Usuario</h6>
            </div>
            <div class="card-body">
                <form method="post" action="<?php echo url('usuario/guardarUsuario'); ?>">
                    <div class="form-group">
                        <label class="small font-weight-bold text-gray-700">Nombre</label>
                        <input type="text" name="nombre" class="form-control form-control-sm" required>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold text-gray-700">Email</label>
                        <input type="email" name="email" class="form-control form-control-sm" required>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i> Se enviará un correo con las credenciales de acceso a esta dirección.
                        </small>
                    </div>

                    <!-- Información sobre contraseña automática -->
                    <div class="alert alert-info" style="background-color:#e0f2fe;border-left:4px solid #3b82f6;border-radius:8px;padding:12px;">
                        <i class="fas fa-key" style="color:#1e40af;"></i>
                        <strong>Contraseña automática:</strong><br>
                        <small style="color:#1e3a8a;">
                            Se generará una contraseña temporal segura y se enviará por correo electrónico.
                            El usuario deberá cambiarla al iniciar sesión por primera vez.
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="small font-weight-bold text-gray-700">Rol</label>
                        <select name="rol" class="form-control form-control-sm">
                            <option value="usuario">Usuario</option>
                            <option value="admin">Admin</option>
                            <option value="superadmin">SuperAdmin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-2">
                        <i class="fas fa-save fa-sm text-white-50 mr-1"></i> Crear Usuario
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php // Footer included from BaseController 
?>