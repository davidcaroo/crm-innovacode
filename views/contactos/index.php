<?php // Layout included from BaseController 
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800">
            <i class="fas fa-address-book text-primary mr-2"></i>
            Contactos de la Empresa
        </h1>
        <p class="mb-0 text-gray-500 small">Administra datos de contacto asociados a la empresa</p>
    </div>
    <div>
        <a href="<?php echo url('contacto/crear', ['empresa_id' => $empresa_id]); ?>" class="btn btn-sm btn-primary shadow-sm mr-2">
            <i class="fas fa-user-plus fa-sm text-white-50 mr-1"></i>
            Nuevo Contacto
        </a>
        <a href="<?php echo url('empresa/index'); ?>" class="btn btn-sm btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Volver a Empresas
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-users mr-2"></i> Listado de Contactos
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tablaContactos" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th><i class="fas fa-user fa-xs mr-1"></i> Nombre</th>
                        <th><i class="fas fa-briefcase fa-xs mr-1"></i> Cargo</th>
                        <th><i class="fas fa-envelope fa-xs mr-1"></i> Email</th>
                        <th><i class="fas fa-phone fa-xs mr-1"></i> Teléfono</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contactos as $c): ?>
                        <tr>
                            <td class="font-weight-bold"><?php echo htmlspecialchars($c->nombre); ?></td>
                            <td>
                                <span class="badge badge-pill badge-light text-dark"><?php echo htmlspecialchars($c->cargo); ?></span>
                            </td>
                            <td>
                                <a href="mailto:<?php echo htmlspecialchars($c->email); ?>" class="text-primary">
                                    <i class="fas fa-envelope fa-xs mr-1"></i>
                                    <?php echo htmlspecialchars($c->email); ?>
                                </a>
                            </td>
                            <td>
                                <a href="tel:<?php echo htmlspecialchars($c->telefono); ?>" class="text-success">
                                    <i class="fas fa-phone fa-xs mr-1"></i>
                                    <?php echo htmlspecialchars($c->telefono); ?>
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="<?php echo url('contacto/editar', ['empresa_id' => $empresa_id, 'id' => $c->id]); ?>" class="btn btn-sm btn-outline-primary mr-1" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="return confirmarEliminacion('<?php echo url('contacto/eliminar', ['id' => $c->id]); ?>', '¿Eliminar el contacto <?php echo htmlspecialchars($c->nombre); ?>?')">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($contactos)): ?>
            <div class="text-center py-4">
                <i class="fas fa-user-slash fa-3x text-gray-300 mb-3"></i>
                <p class="text-gray-500 mb-0">No hay contactos registrados.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    $(document).ready(function() {
        if ($.fn.DataTable) {
            $('#tablaContactos').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                pageLength: 10,
                order: [
                    [0, 'asc']
                ]
            });
        }
    });
</script>

<?php // Footer included from BaseController 
?>