<?php // Layout included from BaseController 
?>

<div class="page-header">
    <div>
        <h2 class="page-title"><span class="mdi mdi-account-group"></span> Usuarios del Sistema</h2>
        <span class="page-subtitle"><?php echo count($usuarios); ?> usuario<?php echo count($usuarios) != 1 ? 's' : ''; ?> en total</span>
    </div>
    <a href="<?php echo BASE_URL; ?>/index.php?controller=usuario&action=crearUsuario" class="btn btn-primary btn-sm">
        <span class="mdi mdi-plus"></span> Nuevo Usuario
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nombre / Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Progreso</th>
                        <th>Creado</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td>
                                <div style="font-weight:700; color:#1e293b;"><?= htmlspecialchars($u->nombre) ?></div>
                                <div style="font-size:0.75rem; color:#64748b;"><?= htmlspecialchars($u->email) ?></div>
                            </td>
                            <td>
                                <?php if ($u->rol === 'superadmin'): ?>
                                    <span class="badge badge-dark" style="font-size:0.7rem; padding:4px 8px;">Superadmin</span>
                                <?php elseif ($u->rol === 'admin'): ?>
                                    <span class="badge badge-primary" style="font-size:0.7rem; padding:4px 8px;">Admin</span>
                                <?php else: ?>
                                    <span class="badge badge-light border" style="font-size:0.7rem; padding:4px 8px;">Usuario</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (($u->estado ?? 'activo') === 'activo'): ?>
                                    <span class="text-success" style="font-size:0.8rem; font-weight:700;">
                                        <i class="bi bi-circle-fill" style="font-size:0.5rem;"></i> Activo
                                    </span>
                                <?php else: ?>
                                    <span class="text-danger" style="font-size:0.8rem; font-weight:700;">
                                        <i class="bi bi-circle-fill" style="font-size:0.5rem;"></i> Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-size:0.8rem; font-weight:700; color:#1e40af;">
                                    <?= $u->total_empresas ?? 0 ?> <span style="font-weight:400; color:#64748b;">empresas</span>
                                </div>
                            </td>
                            <td><small class="text-muted"><?= htmlspecialchars(date('d/m/Y', strtotime($u->creado_en))) ?></small></td>
                            <td class="text-right">
                                <div class="d-flex justify-content-end" style="gap:5px;">
                                    <?php if ($_SESSION['usuario_rol'] === 'superadmin' && $u->id != $_SESSION['usuario_id']): ?>
                                        <a href="<?php echo BASE_URL; ?>/index.php?controller=usuario&action=impersonate&id=<?= $u->id ?>"
                                            class="btn btn-sm btn-info text-white" style="border-radius:6px; font-weight:700;"
                                            title="Espectar (Entrar como este usuario)">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    <?php endif; ?>

                                    <a href="<?php echo BASE_URL; ?>/index.php?controller=usuario&action=editarUsuario&id=<?= $u->id ?>"
                                        class="btn btn-sm btn-light border" style="border-radius:6px; color:#475569;" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <?php if ($u->id != $_SESSION['usuario_id']): ?>
                                        <a href="<?php echo BASE_URL; ?>/index.php?controller=usuario&action=eliminarUsuario&id=<?= $u->id ?>"
                                            class="btn btn-sm btn-light border text-danger" style="border-radius:6px;"
                                            title="Eliminar"
                                            onclick="return confirm('Eliminar usuario <?= htmlspecialchars($u->nombre) ?>?')">
                                            <i class="bi bi-trash"></i>
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