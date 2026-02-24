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
                    </div>
                    <div class="form-group">
                        <label><small class="font-weight-bold text-uppercase text-muted">Password</small></label>
                        <input type="password" name="password" class="form-control" required>
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