<?php // Layout included from BaseController 
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Empresas</h1>
    <div>
        <a href="<?php echo url('empresa/importar'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm text-white">
            <i class="fas fa-upload fa-sm text-white-50"></i> Importar
        </a>
        <a href="<?php echo url('empresa/pipeline'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-columns fa-sm text-white-50"></i> Ver Pipeline
        </a>
        <a href="<?php echo url('empresa/crear'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nueva Empresa
        </a>
    </div>
</div>

<?php if (isset($_GET['import']) && $_GET['import'] == 'success'): ?>
    <div class="alert alert-success border-0 shadow-sm py-2 px-3 mb-3 d-flex align-items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <div class="small fw-semibold">Importación completada con éxito. Los datos ya están disponibles en tiempo real.</div>
    </div>
<?php endif; ?>

<!-- Barra de Búsqueda -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Buscador</h6>
    </div>
    <div class="card-body">
        <form id="formBuscar" class="form-inline">
            <div class="input-group w-100">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
                <input type="text" name="buscar" id="inputBuscar" class="form-control" placeholder="Buscar por razón social, departamento, ciudad o correo..." value="<?php echo htmlspecialchars($buscar); ?>" autocomplete="off">
            </div>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Listado de Empresas</h6>
        <small class="text-muted"><?php echo count($empresas); ?> registros</small>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-hover dataTable" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Razón Social</th>
                        <th>Dpto</th>
                        <th>Ciudad</th>
                        <th>Actividad</th>
                        <th>Correo Comercial</th>
                        <th>Etapa Venta</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($empresas)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fas fa-building fa-2x mb-2"></i><br>
                                <?php if (!empty($buscar)): ?>
                                    <strong>No se encontraron empresas con el término "<?php echo htmlspecialchars($buscar); ?>"</strong>
                                    <br><small>Intenta con otro término de búsqueda</small>
                                <?php else: ?>
                                    No hay empresas registradas aun.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($empresas as $e): ?>
                            <tr>
                                <td class="font-weight-bold"><?= htmlspecialchars($e->razon_social) ?></td>
                                <td><?= htmlspecialchars($e->dpto) ?></td>
                                <td><?= htmlspecialchars($e->ciudad) ?></td>
                                <td><?= htmlspecialchars($e->actividad_economica) ?></td>
                                <td><?= htmlspecialchars($e->correo_comercial) ?></td>
                                <td>
                                    <?php
                                    $etapa = strtolower($e->etapa_venta ?? 'prospectado');
                                    $labels = ['prospectado' => 'Prospectado', 'contactado' => 'Contactado', 'negociacion' => 'Negociación', 'ganado' => 'Ganado', 'perdido' => 'Perdido'];
                                    $badgeMap = ['prospectado' => 'info', 'contactado' => 'warning', 'negociacion' => 'primary', 'ganado' => 'success', 'perdido' => 'danger'];
                                    ?>
                                    <span class="badge badge-pill badge-<?= $badgeMap[$etapa] ?? 'secondary' ?>">
                                        <?= $labels[$etapa] ?? ucfirst($etapa) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?php echo url('contacto/index', ['empresa_id' => $e->id]); ?>"
                                            class="btn btn-sm btn-outline-info" title="Contactos">
                                            <i class="fas fa-address-book fa-sm"></i>
                                        </a>
                                        <a href="<?php echo url('trazabilidad/index', ['empresa_id' => $e->id]); ?>"
                                            class="btn btn-sm btn-outline-success" title="Trazabilidad">
                                            <i class="fas fa-eye fa-sm"></i>
                                        </a>
                                        <a href="<?php echo url('empresa/editar', ['id' => $e->id]); ?>"
                                            class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit fa-sm"></i>
                                        </a>
                                        <a href="#"
                                            class="btn btn-sm btn-outline-danger" title="Eliminar"
                                            onclick="return confirmarEliminacion('<?php echo url('empresa/eliminar', ['id' => $e->id]); ?>', '¿Eliminar la empresa <?php echo htmlspecialchars($e->razon_social); ?>?')">
                                            <i class="fas fa-trash-alt fa-sm"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputBuscar = document.getElementById('inputBuscar');
        const formBuscar = document.getElementById('formBuscar');

        if (formBuscar) {
            formBuscar.addEventListener('submit', function(e) {
                e.preventDefault();
            });
        }

        if (window.jQuery && jQuery.fn.DataTable && document.getElementById('dataTable')) {
            const table = jQuery('#dataTable').DataTable({
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                order: [
                    [0, 'asc']
                ],
                autoWidth: true,
                responsive: true,
                dom: 't<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                },
                columnDefs: [{
                    targets: [6],
                    orderable: false,
                    searchable: false
                }]
            });

            if (inputBuscar) {
                inputBuscar.addEventListener('input', function() {
                    table.search(this.value).draw();
                });
            }
        }

        if (inputBuscar && inputBuscar.value === '') {
            inputBuscar.focus();
        }

        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                if (inputBuscar) {
                    inputBuscar.focus();
                    inputBuscar.select();
                }
            }
        });
    });
</script>

<?php // Footer included from BaseController 
?>