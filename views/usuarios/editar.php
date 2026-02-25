<?php // Layout included from BaseController 
?>

<div class="page-header">
    <div>
        <h2 class="page-title"><i class="bi bi-person-gear"></i> Editar Usuario</h2>
        <span class="page-subtitle">Actualiza el perfil y permisos del usuario</span>
    </div>
    <a href="<?php echo url('usuario/lista'); ?>" class="btn btn-sm btn-danger text-white" style="border-radius:6px; font-weight:800;">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="row">
    <div class="col-12 col-md-8 col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="post" action="<?php echo url('usuario/actualizarUsuario'); ?>">
                    <input type="hidden" name="id" value="<?= $usuario->id ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label style="font-size:0.82rem;font-weight:600;color:#475569;">Nombre Completo</label>
                                <input type="text" name="nombre" class="form-control form-control-sm" value="<?= htmlspecialchars($usuario->nombre) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label style="font-size:0.82rem;font-weight:600;color:#475569;">Email Institucional</label>
                                <input type="email" name="email" class="form-control form-control-sm" value="<?= htmlspecialchars($usuario->email) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label style="font-size:0.82rem;font-weight:600;color:#475569;">Rol en el Sistema</label>
                                <select name="rol" class="form-control form-control-sm shadow-sm">
                                    <option value="usuario" <?= (!isset($usuario->rol) || $usuario->rol === 'usuario') ? 'selected' : '' ?>>Usuario Operativo</option>
                                    <option value="admin" <?= (isset($usuario->rol) && $usuario->rol === 'admin') ? 'selected' : '' ?>>Administrador Local</option>
                                    <option value="superadmin" <?= (isset($usuario->rol) && $usuario->rol === 'superadmin') ? 'selected' : '' ?>>Superadmin (Global)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label style="font-size:0.82rem;font-weight:600;color:#475569;">Estado de la Cuenta</label>
                                <select name="estado" class="form-control form-control-sm shadow-sm">
                                    <option value="activo" <?= (!isset($usuario->estado) || $usuario->estado === 'activo') ? 'selected' : '' ?>>Activo</option>
                                    <option value="inactivo" <?= (isset($usuario->estado) && $usuario->estado === 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label style="font-size:0.82rem;font-weight:600;color:#475569;">Restablecer Contrasena</label>
                        <input type="password" name="password" class="form-control form-control-sm" placeholder="Dejar vacio para no cambiar...">
                        <small class="form-text text-muted">Mínimo 8 caracteres si se desea cambiar.</small>
                    </div>

                    <div class="d-flex justify-content-end" style="gap:10px;">
                        <a href="<?php echo url('usuario/lista'); ?>"
                            class="btn btn-danger text-white" style="border-radius:8px; padding:8px 22px; font-weight:800;">Cancelar</a>
                        <button type="submit" class="btn btn-primary" style="border-radius:8px; padding:8px 28px; font-weight:700;">
                            <i class="bi bi-save"></i> Actualizar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php // Footer included from BaseController 
?>