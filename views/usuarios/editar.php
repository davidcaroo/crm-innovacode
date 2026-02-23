<?php // Layout included from BaseController 
?>

<div class="page-header">
    <h2 class="page-title"><span class="mdi mdi-account-edit"></span> Editar Usuario</h2>
    <a href="<?php echo BASE_URL; ?>/index.php?controller=usuario&action=lista" class="btn btn-sm btn-outline-secondary">
        <span class="mdi mdi-arrow-left"></span> Volver
    </a>
</div>

<div class="row">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="post" action="<?php echo BASE_URL; ?>/index.php?controller=usuario&action=actualizarUsuario">
                    <input type="hidden" name="id" value="<?= $usuario->id ?>">
                    <div class="form-group">
                        <label><small class="font-weight-bold text-uppercase text-muted">Nombre</small></label>
                        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario->nombre) ?>" required>
                    </div>
                    <div class="form-group">
                        <label><small class="font-weight-bold text-uppercase text-muted">Email</small></label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario->email) ?>" required>
                    </div>
                    <div class="form-group">
                        <label><small class="font-weight-bold text-uppercase text-muted">Rol</small></label>
                        <select name="rol" class="form-control">
                            <option value="usuario" <?= $usuario->rol === 'usuario' ? 'selected' : '' ?>>Usuario</option>
                            <option value="admin" <?= $usuario->rol === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="superadmin" <?= $usuario->rol === 'superadmin' ? 'selected' : '' ?>>SuperAdmin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><small class="font-weight-bold text-uppercase text-muted">Nueva Contrasena (dejar vacio para no cambiar)</small></label>
                        <input type="password" name="password" class="form-control" placeholder="Nueva contrasena...">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block mt-2">
                        <span class="mdi mdi-content-save"></span> Guardar Cambios
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php // Footer included from BaseController 
?>