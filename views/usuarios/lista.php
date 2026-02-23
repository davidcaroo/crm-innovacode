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
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Creado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td class="font-weight-bold"><?= htmlspecialchars($u->nombre) ?></td>
                            <td><?= htmlspecialchars($u->email) ?></td>
                            <td>
                                <?php if ($u->rol === 'superadmin'): ?>
                                    <span class="badge-etapa badge-ganado">SuperAdmin</span>
                                <?php elseif ($u->rol === 'admin'): ?>
                                    <span class="badge-etapa badge-contactado">Admin</span>
                                <?php else: ?>
                                    <span class="badge-etapa badge-prospectado">Usuario</span>
                                <?php endif; ?>
                            </td>
                            <td><small><?= htmlspecialchars(date('d/m/Y', strtotime($u->creado_en))) ?></small></td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>/index.php?controller=usuario&action=editarUsuario&id=<?= $u->id ?>"
                                    class="btn btn-sm btn-outline-primary mx-1" style="border-radius:5px;">
                                    <span class="mdi mdi-pencil"></span>
                                </a>
                                <?php if ($u->id != $_SESSION['usuario_id']): ?>
                                    <a href="<?php echo BASE_URL; ?>/index.php?controller=usuario&action=eliminarUsuario&id=<?= $u->id ?>"
                                        class="btn btn-sm btn-outline-danger mx-1" style="border-radius:5px;"
                                        onclick="return confirm('Eliminar usuario <?= htmlspecialchars($u->nombre) ?>?')">
                                        <span class="mdi mdi-delete"></span>
                                    </a>
                                <?php endif; ?>
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