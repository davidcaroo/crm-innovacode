<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-import mr-2 text-primary"></i> Importación Masiva</h1>
    <a href="<?php echo url('empresa/index'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Volver a Empresas
    </a>
</div>

<p class="mb-4 text-gray-700">Carga de empresas al sistema de forma masiva mediante archivo CSV (Separado por comas o punto y coma)</p>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4 h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-ol mr-2"></i>Instrucciones de Carga</h6>
            </div>
            <div class="card-body">
                <p class="small text-gray-600 mb-3">Para garantizar que la importación sea exitosa, asegúrese de que su archivo CSV cumpla con el siguiente orden exacto de columnas (Encabezados):</p>

                <div class="bg-light p-3 rounded" style="border: 2px dashed #d1d3e2;">
                    <code class="text-primary d-block" style="font-size: 0.85rem;">
                        razon_social, dpto, ciudad, actividad_economica, correo_comercial, aplica(Si/No), etapa_venta, observaciones
                    </code>
                </div>

                <ul class="mt-3 small text-gray-700">
                    <li class="mb-1"><strong class="text-dark">razon_social:</strong> Nombre de la empresa (Obligatorio).</li>
                    <li class="mb-1"><strong class="text-dark">etapa_venta:</strong> prospectado, contactado, negociacion, seguimiento, ganado, perdido.</li>
                    <li>El archivo debe estar codificado en <strong class="text-dark">UTF-8</strong> para reconocer tildes y caracteres especiales.</li>
                </ul>

                <div class="alert alert-info border-left-info mb-0 mt-4 small shadow-sm">
                    <i class="fas fa-info-circle mr-1"></i> El sistema detectará automáticamente si el separador es coma (,) o punto y coma (;).
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4 h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-upload mr-2"></i>Selección de Archivo</h6>
            </div>
            <div class="card-body">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger border-left-danger shadow-sm mb-4 small">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <?php
                        if ($_GET['error'] == 'upload') echo "Error al subir el archivo.";
                        elseif ($_GET['error'] == 'db') echo "Error al procesar los datos en el sistema.";
                        elseif ($_GET['error'] == 'empty') echo "El archivo parece estar vacío o mal formateado.";
                        else echo "Ocurrió un error inesperado durante la importación.";
                        ?>
                    </div>
                <?php endif; ?>
                
                <p class="small text-gray-600 mb-4">Seleccione su archivo preparado desde su dispositivo local y proceda presionado el botón azul inferior.</p>

                <form action="<?php echo url('empresa/procesarImportacion'); ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group mb-4 mt-3">
                        <label for="archivo_csv" class="font-weight-bold text-gray-800 small">Archivo CSV (.csv)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input cursor-pointer" name="archivo_csv" id="archivo_csv" accept=".csv" required>
                            <label class="custom-file-label" for="archivo_csv" data-browse="Buscar CSV">Seleccionar archivo...</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block shadow-sm font-weight-bold mt-5">
                        <i class="fas fa-database mr-2"></i> Iniciar Importación Masiva
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Mostrar nombre del archivo al seleccionarlo (Bootstrap 4 Custom File)
    document.addEventListener('DOMContentLoaded', function() {
        var input = document.getElementById('archivo_csv');
        if (input) {
            input.addEventListener('change', function(e) {
                var fileName = e.target.files[0] ? e.target.files[0].name : 'Seleccionar archivo...';
                var nextSibling = e.target.nextElementSibling;
                if (nextSibling) {
                    nextSibling.innerText = fileName;
                }
            });
        }
    });
</script>