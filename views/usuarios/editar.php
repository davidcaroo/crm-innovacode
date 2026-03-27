<?php // Layout included from BaseController 
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800"><i class="fas fa-user-cog text-primary mr-2"></i> Editar Usuario</h1>
        <p class="mb-0 text-gray-500 small">Actualiza el perfil y permisos del usuario</p>
    </div>
    <a href="<?php echo url('usuario/lista'); ?>" class="btn btn-sm btn-outline-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm mr-1"></i> Volver
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-edit mr-2"></i> Datos del Usuario</h6>
            </div>
            <div class="card-body">
                <form method="post" action="<?php echo url('usuario/actualizarUsuario'); ?>">
                    <input type="hidden" name="id" value="<?= $usuario->id ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold text-gray-700">Nombre Completo</label>
                                <input type="text" name="nombre" class="form-control form-control-sm" value="<?= htmlspecialchars($usuario->nombre) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold text-gray-700">Email Institucional</label>
                                <input type="email" name="email" class="form-control form-control-sm" value="<?= htmlspecialchars($usuario->email) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold text-gray-700">Rol en el Sistema</label>
                                <select name="rol" class="form-control form-control-sm shadow-sm">
                                    <option value="usuario" <?= (!isset($usuario->rol) || $usuario->rol === 'usuario') ? 'selected' : '' ?>>Usuario Operativo</option>
                                    <option value="admin" <?= (isset($usuario->rol) && $usuario->rol === 'admin') ? 'selected' : '' ?>>Administrador Local</option>
                                    <option value="superadmin" <?= (isset($usuario->rol) && $usuario->rol === 'superadmin') ? 'selected' : '' ?>>Superadmin (Global)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold text-gray-700">Estado de la Cuenta</label>
                                <select name="estado" class="form-control form-control-sm shadow-sm">
                                    <option value="activo" <?= (!isset($usuario->estado) || $usuario->estado === 'activo') ? 'selected' : '' ?>>Activo</option>
                                    <option value="inactivo" <?= (isset($usuario->estado) && $usuario->estado === 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="small font-weight-bold text-gray-700">Restablecer Contrasena</label>
                        <input type="password" name="password" class="form-control form-control-sm" placeholder="Dejar vacio para no cambiar...">
                        <small class="form-text text-muted">Mínimo 8 caracteres si se desea cambiar.</small>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="<?php echo url('usuario/lista'); ?>"
                            class="btn btn-danger mr-2"><i class="fas fa-times fa-sm text-white-50 mr-1"></i> Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save fa-sm text-white-50 mr-1"></i> Actualizar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php // Footer included from BaseController 
?>