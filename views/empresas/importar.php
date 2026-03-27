<div class="page-header">
    <div>
        <h2 class="page-title"><i class="fa-solid fa-file-import"></i> Importación Masiva</h2>
        <span class="page-subtitle">Carga de empresas mediante archivo CSV (Separado por comas o punto y coma)</span>
    </div>
    <div>
        <a href="<?php echo url('empresa/index'); ?>" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title fw-bold">Instrucciones de Carga</h5>
                <p class="text-muted small">Para garantizar que la importación sea exitosa, asegúrese de que su archivo CSV cumpla con el siguiente orden de columnas:</p>

                <div class="bg-light p-3 rounded" style="border: 1px dashed #cbd5e1;">
                    <code style="font-size: 0.85rem; color: #1e40af;">
                        razon_social, dpto, ciudad, actividad_economica, correo_comercial, aplica(Si/No), etapa_venta, observaciones
                    </code>
                </div>

                <ul class="mt-3 small text-muted">
                    <li><strong>razon_social:</strong> Nombre de la empresa (Obligatorio).</li>
                    <li><strong>etapa_venta:</strong> prospectado, contactado, negociacion, ganado, perdido.</li>
                    <li>El archivo debe estar codificado en <strong>UTF-8</strong> para reconocer tildes y caracteres especiales.</li>
                </ul>

                <div class="alert alert-info py-2 px-3 border-0 mt-3" style="font-size: 0.82rem;">
                    <i class="fa-solid fa-circle-info"></i>
                    El sistema detectará automáticamente si el separador es coma (,) o punto y coma (;).
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title fw-bold">Seleccionar Archivo</h5>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger py-2 px-3 border-0 mb-3" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <?php
                        if ($_GET['error'] == 'upload') echo "Error al subir el archivo.";
                        elseif ($_GET['error'] == 'db') echo "Error al procesar los datos en el sistema.";
                        elseif ($_GET['error'] == 'empty') echo "El archivo parece estar vacío o mal formateado.";
                        else echo "Ocurrió un error inesperado.";
                        ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo url('empresa/procesarImportacion'); ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-4 mt-3">
                        <label for="archivo_csv" class="form-label fw-semibold small">Archivo CSV (.csv)</label>
                        <input type="file" name="archivo_csv" id="archivo_csv" class="form-control" accept=".csv" required style="border-radius: 8px; padding: 10px;">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 shadow-sm" style="border-radius: 8px; font-weight: 600;">
                        <i class="fa-solid fa-database"></i> Iniciar Importación
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>