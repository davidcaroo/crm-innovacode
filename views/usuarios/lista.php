<?php // Layout included from BaseController 
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800"><i class="fas fa-users text-primary mr-2"></i> Usuarios del Sistema</h1>
        <p class="mb-0 text-gray-500 small"><?php echo count($usuarios); ?> usuario<?php echo count($usuarios) != 1 ? 's' : ''; ?> en total</p>
    </div>
    <a href="<?php echo url('usuario/crearUsuario'); ?>" class="btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-user-plus fa-sm text-white-50 mr-1"></i> Nuevo Usuario
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0" id="tablaUsuarios" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Nombre / Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Progreso</th>
                        <th>Creado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td>
                                <div class="font-weight-bold text-dark"><?= htmlspecialchars($u->nombre) ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($u->email) ?></div>
                            </td>
                            <td>
                                <?php if ($u->rol === 'superadmin'): ?>
                                    <span class="badge badge-pill badge-dark">Superadmin</span>
                                <?php elseif ($u->rol === 'admin'): ?>
                                    <span class="badge badge-pill badge-primary">Admin</span>
                                <?php else: ?>
                                    <span class="badge badge-pill badge-light text-dark border">Usuario</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (($u->estado ?? 'activo') === 'activo'): ?>
                                    <span class="text-success small font-weight-bold">
                                        <i class="fas fa-circle fa-xs mr-1"></i> Activo
                                    </span>
                                <?php else: ?>
                                    <span class="text-danger small font-weight-bold">
                                        <i class="fas fa-circle fa-xs mr-1"></i> Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="small font-weight-bold text-primary">
                                    <?= $u->total_empresas ?? 0 ?> <span class="font-weight-normal text-muted">empresas</span>
                                </div>
                            </td>
                            <td><small class="text-muted"><?= htmlspecialchars(date('d/m/Y', strtotime($u->creado_en))) ?></small></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <?php if ($_SESSION['usuario_rol'] === 'superadmin' && $u->id != $_SESSION['usuario_id']): ?>
                                        <a href="<?php echo url('usuario/impersonate', ['id' => $u->id]); ?>"
                                            class="btn btn-sm btn-outline-info"
                                            title="Espectar (Entrar como este usuario)">
                                            <i class="fas fa-eye fa-sm"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!($u->rol === 'superadmin' && $u->id != $_SESSION['usuario_id'])): ?>
                                        <a href="<?php echo url('usuario/editarUsuario', ['id' => $u->id]); ?>"
                                            class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit fa-sm"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($u->id != $_SESSION['usuario_id'] && $u->rol !== 'superadmin'): ?>
                                        <a href="#"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Eliminar"
                                            onclick="return confirmarEliminacion('<?php echo url('usuario/eliminarUsuario', ['id' => $u->id]); ?>', '¿Eliminar al usuario <?php echo htmlspecialchars($u->nombre); ?>?')">
                                            <i class="fas fa-trash-alt fa-sm"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php // Footer included from BaseController 
?>