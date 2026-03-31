<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Redactar Mensaje</h1>
    <div>
        <a href="<?php echo url('emailMarketing/index'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Volver a Destinatarios
        </a>
    </div>
</div>

<div class="row">
    <!-- Formulario de Redacción -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Configuración del Mensaje</h6>
            </div>
            <div class="card-body">
                <form action="<?php echo url('emailMarketing/enviar'); ?>" method="POST">
                    <input type="hidden" name="empresas_ids" value='<?php echo htmlspecialchars($empresas_ids_json); ?>'>

                    <div class="form-group">
                        <label for="plantilla_id">Usar Plantilla (Opcional)</label>
                        <select name="plantilla_id" id="plantilla_id" class="form-control" onchange="loadPlantilla(this.value)">
                            <option value="">-- Redactar desde cero --</option>
                            <?php foreach ($plantillas as $p): ?>
                                <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="asunto" class="font-weight-bold">Asunto <span class="text-danger">*</span></label>
                        <input type="text" name="asunto" id="asunto" class="form-control" placeholder="Ej. Propuesta de Servicios..." required>
                    </div>

                    <div class="form-group">
                        <label for="cuerpo_html" class="font-weight-bold">Cuerpo del Mensaje (HTML permitido) <span class="text-danger">*</span></label>
                        <textarea name="cuerpo_html" id="cuerpo_html" class="form-control" rows="12" required></textarea>
                        <small class="form-text text-muted">
                            Variables disponibles: <span class="badge badge-secondary">{{empresa}}</span> será reemplazado por la Razón Social.
                        </small>
                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-info btn-lg btn-block" onclick="mostrarVistaPrevia()">
                                <i class="fas fa-eye mr-2"></i> Vista Previa
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" id="btnEnviarCampaña" class="btn btn-success btn-lg btn-block">
                                <i class="fas fa-paper-plane mr-2"></i> Enviar a <?php echo count($empresas); ?> Destinatarios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Lista de Destinatarios -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Destinatarios Seleccionados</h6>
                <span class="badge badge-primary badge-pill"><?php echo count($empresas); ?></span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush" style="max-height: 500px; overflow-y: auto;">
                    <?php foreach ($empresas as $empresa): ?>
                        <li class="list-group-item">
                            <strong><?php echo htmlspecialchars($empresa['razon_social']); ?></strong><br>
                            <small class="text-muted"><i class="fas fa-envelope mr-1"></i> <?php echo htmlspecialchars($empresa['correo_comercial']); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Vista Previa -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="previewModalLabel">Vista Previa del Mensaje</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="previewModalBody" style="background-color: #f8f9fc;">
          <!-- El contenido se cargará aquí -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
function loadPlantilla(plantillaId) {
    if (!plantillaId) {
        document.getElementById('asunto').value = '';
        document.getElementById('cuerpo_html').value = '';
        return;
    }

    // Ajax call to get template content
    const baseUrl = "<?php echo url('emailMarketing/obtenerPlantillaAjax'); ?>";
    const separator = baseUrl.includes('?') ? '&' : '?';
    
    fetch(`${baseUrl}${separator}id=${plantillaId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                document.getElementById('asunto').value = data.asunto;
                document.getElementById('cuerpo_html').value = data.cuerpo_html;
            }
        })
        .catch(err => console.error(err));
}

function mostrarVistaPrevia() {
    const cuerpo = document.getElementById('cuerpo_html').value;
    const asunto = document.getElementById('asunto').value;
    
    // Simular el reemplazo de la variable {{empresa}} para la vista previa
    let previewHtml = cuerpo.replace(/{{empresa}}/g, '<span class="badge badge-warning">NOMBRE DE LA EMPRESA</span>');
    
    document.getElementById('previewModalLabel').innerText = 'Vista Previa: ' + (asunto || 'Sin Asunto');
    document.getElementById('previewModalBody').innerHTML = previewHtml;
    $('#previewModal').modal('show');
}

// Confirmación de envío con SweetAlert2
document.getElementById('btnEnviarCampaña').addEventListener('click', function(e) {
    e.preventDefault();
    const form = this.closest('form');
    
    // Validar campos requeridos nativamente antes de mostrar el alerta
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    Swal.fire({
        title: '¿Confirmar envío masivo?',
        text: "Se enviará este correo a los <?php echo count($empresas); ?> destinatarios seleccionados.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, enviar ahora',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Enviando...',
                text: 'Por favor espere mientras se procesan los correos.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            form.submit();
        }
    });
});
</script>
