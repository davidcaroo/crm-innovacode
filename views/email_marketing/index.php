<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Email Marketing</h1>
    <div>
        <a href="<?php echo url('emailMarketing/plantillas'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm text-white">
            <i class="fas fa-file-code fa-sm text-white-50"></i> Gestionar Plantillas
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Seleccionar Destinatarios</h6>
    </div>
    <div class="card-body">
        <form action="<?php echo url('emailMarketing/redactar'); ?>" method="POST">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>Razón Social</th>
                            <th>Correo Comercial</th>
                            <th>Etapa Venta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($empresas as $empresa): ?>
                            <tr>
                                <td class="text-center">
                                    <?php if (!empty($empresa['correo_comercial'])): ?>
                                        <input type="checkbox" name="empresas_ids[]" value="<?php echo $empresa['id']; ?>" class="checkItem">
                                    <?php else: ?>
                                        <span class="text-danger" title="Sin correo" data-toggle="tooltip"><i class="fas fa-exclamation-triangle"></i></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($empresa['razon_social']); ?></td>
                                <td><?php echo htmlspecialchars($empresa['correo_comercial'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge badge-primary"><?php echo htmlspecialchars(ucfirst($empresa['etapa_venta'] ?? '')); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary" id="btnRedactar" disabled>
                    <i class="fas fa-paper-plane"></i> Redactar / Configurar Mensaje
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.checkItem');
    const btnRedactar = document.getElementById('btnRedactar');

    function updateButton() {
        const checked = document.querySelectorAll('.checkItem:checked').length;
        btnRedactar.disabled = checked === 0;
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateButton();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const checked = document.querySelectorAll('.checkItem:checked').length;
            if (selectAll) {
                selectAll.checked = checked === checkboxes.length && checkboxes.length > 0;
            }
            updateButton();
        });
    });
    
    // Enable datatables for search and pagination (without server side)
    if (window.jQuery && jQuery.fn.DataTable && document.getElementById('dataTable')) {
        jQuery('#dataTable').DataTable({
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' },
            columnDefs: [{ targets: [0], orderable: false }]
        });
    }
});
</script>
