<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Plantillas de Email</h1>
    <div>
        <a href="<?php echo url('emailMarketing/crearPlantilla'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nueva Plantilla
        </a>
        <a href="<?php echo url('emailMarketing/index'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Volver a Envíos
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Listado de Plantillas HTML</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Asunto Predefinido</th>
                        <th>Creador</th>
                        <th>Fecha Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($plantillas)): ?>
                        <tr><td colspan="5" class="text-center text-muted">No existen plantillas guardadas.</td></tr>
                    <?php else: ?>
                        <?php foreach($plantillas as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($p['asunto']); ?></td>
                                <td><?php echo htmlspecialchars($p['usuario_nombre']); ?></td>
                                <td><?php echo date('d-m-Y H:i', strtotime($p['creado_en'])); ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info text-white me-1" onclick="verPlantilla(`<?php echo htmlspecialchars($p['cuerpo_html']); ?>`)">
                                        <i class="fas fa-eye"></i> Previa
                                    </button>
                                    <a href="#" 
                                       class="btn btn-sm btn-danger text-white"
                                       onclick="return confirmarEliminacion(`<?php echo url('emailMarketing/eliminarPlantilla', ['id' => $p['id']]); ?>`, '¿Desea eliminar esta plantilla?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Vista Previa -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="previewModalLabel">Vista Previa</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="previewModalBody">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
function verPlantilla(htmlContent) {
    document.getElementById('previewModalBody').innerHTML = htmlContent;
    $('#previewModal').modal('show');
}
if (window.jQuery && jQuery.fn.DataTable && document.getElementById('dataTable')) {
    $('#dataTable').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' }
    });
}
</script>
