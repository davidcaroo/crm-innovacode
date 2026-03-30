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
        <small class="text-muted" id="totalRecordsBadge"><?php echo $totalEmpresas ?? 0; ?> registros en total</small>
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
                    <!-- Llenado usando AJAX Server-Side -->
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
                pageLength: 10,
                lengthMenu: [10, 15, 25, 50, 100],
                serverSide: true,
                processing: true,
                searching: false, // Ocultar buscador por defecto ya que usamos el personalizado
                ajax: {
                    url: '<?php echo url('empresa/datosDataTables'); ?>',
                    type: 'POST',
                    data: function(d) {
                        d.search.value = inputBuscar ? inputBuscar.value : '';
                    }
                },
                order: [
                    [0, 'asc']
                ],
                autoWidth: true,
                responsive: true,
                dom: 't<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json',
                    processing: '<div class="text-center text-primary mt-3"><i class="fas fa-circle-notch fa-spin fa-2x"></i> <br> Cargando información...</div>'
                },
                columnDefs: [{
                    targets: [6],
                    orderable: false,
                    searchable: false
                }],
                drawCallback: function(settings) {
                    const badge = document.getElementById('totalRecordsBadge');
                    if (badge) {
                        badge.innerText = settings._iRecordsTotal + ' registros en total (' + settings._iRecordsDisplay + ' filtrados)';
                    }
                }
            });

            if (inputBuscar) {
                let searchTimeout;
                inputBuscar.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        table.draw();
                    }, 400); // 400ms de debouncing para no saturar al servidor
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