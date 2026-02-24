<?php // Layout included from BaseController 
?>
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-primary">Contactos de la Empresa</h3>
        <a href="<?php echo url('contacto/crear', ['empresa_id' => $empresa_id]); ?>" class="btn btn-primary">
            <span class="mdi mdi-plus"></span> Nuevo Contacto
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white">
            <thead class="thead-light">
                <tr>
                    <th>Nombre</th>
                    <th>Cargo</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contactos as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c->nombre) ?></td>
                        <td><?= htmlspecialchars($c->cargo) ?></td>
                        <td><?= htmlspecialchars($c->email) ?></td>
                        <td><?= htmlspecialchars($c->telefono) ?></td>
                        <td>
                            <a href="<?php echo url('contacto/editar', ['empresa_id' => $empresa_id, 'id' => $c->id]); ?>" class="btn btn-sm btn-outline-primary"><span class="mdi mdi-pencil"></span></a>
                            <a href="#" class="btn btn-sm btn-outline-danger" onclick="return confirmarEliminacion('<?php echo url('contacto/eliminar', ['id' => $c->id]); ?>', '¿Eliminar el contacto <?php echo htmlspecialchars($c->nombre); ?>?')"><span class="mdi mdi-delete"></span></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <a href="<?php echo url('empresa/index'); ?>" class="btn btn-info mt-3"><span class="mdi mdi-arrow-left"></span> Volver a Empresas</a>
</div>
<?php // Footer included from BaseController 
?>