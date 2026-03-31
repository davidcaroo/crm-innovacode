<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Crear Plantilla</h1>
    <div>
        <a href="<?php echo url('emailMarketing/plantillas'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Volver a Plantillas
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Formulario de Plantilla HTML</h6>
    </div>
    <div class="card-body">
        <form action="<?php echo url('emailMarketing/guardarPlantilla'); ?>" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="nombre" class="font-weight-bold">Nombre para identificar la plantilla <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ej. Propuesta Económica 2026..." required>
                </div>
                <div class="col-md-6 form-group">
                    <label for="asunto" class="font-weight-bold">Asunto del Correo Predefinido <span class="text-danger">*</span></label>
                    <input type="text" name="asunto" id="asunto" class="form-control" placeholder="Ej. Tu Propuesta de Servicios..." required>
                </div>
            </div>

            <div class="form-group mt-3">
                <textarea name="cuerpo_html" id="cuerpo_html" class="form-control" rows="15" placeholder="Redacta o pega aquí el contenido..."></textarea>
                <small class="form-text text-info mt-2">
                    <i class="fas fa-info-circle"></i> Puedes incluir la variable <b>{{empresa}}</b> para insertar el nombre del cliente dinámicamente.
                </small>
            </div>

            <div class="form-group mb-4">
                <label for="adjuntos" class="font-weight-bold"><i class="fas fa-paperclip mr-1"></i> Añadir Archivos Adjuntos (PDF, Doc, etc.)</label>
                <input type="file" name="adjuntos[]" id="adjuntos" class="form-control-file" multiple>
                <small class="text-muted">Puedes seleccionar varios archivos que se enviarán junto con la plantilla.</small>
            </div>

            <hr>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save mr-2"></i> Guardar Plantilla
            </button>
        </form>
    </div>
</div>

<!-- Estilos y Scripts de Summernote -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/lang/summernote-es-ES.min.js"></script>

<script>
$(document).ready(function() {
    $('#cuerpo_html').summernote({
        placeholder: 'Redacta tu propuesta o pega aquí el diseño. Las imágenes pegadas se procesarán automáticamente...',
        tabsize: 2,
        height: 400,
        lang: 'es-ES',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear', 'italic']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
});
</script>
