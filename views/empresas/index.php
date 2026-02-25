<?php // Layout included from BaseController 
?>

<div class="page-header">
    <div>
        <h2 class="page-title"><span class="mdi mdi-domain"></span> Empresas</h2>
        <span class="page-subtitle"><?php echo count($empresas); ?> empresa<?php echo count($empresas) != 1 ? 's' : ''; ?> <?php echo !empty($buscar) ? 'encontrada' . (count($empresas) != 1 ? 's' : '') : 'registrada' . (count($empresas) != 1 ? 's' : ''); ?></span>
    </div>
    <div class="d-flex" style="gap:8px;">
        <a href="<?php echo url('empresa/importar'); ?>"
            class="btn btn-sm btn-info shadow-sm text-white" style="border-radius:6px; font-weight:700;">
            <span class="mdi mdi-upload"></span> Importar
        </a>
        <a href="<?php echo url('empresa/pipeline'); ?>"
            class="btn btn-sm btn-outline-primary shadow-sm" style="border-radius:6px;">
            <span class="mdi mdi-view-column"></span> Ver Pipeline
        </a>
        <a href="<?php echo url('empresa/crear'); ?>"
            class="btn btn-sm btn-primary shadow-sm" style="border-radius:6px;">
            <span class="mdi mdi-plus"></span> Nueva Empresa
        </a>
    </div>
</div>

<?php if (isset($_GET['import']) && $_GET['import'] == 'success'): ?>
    <div class="alert alert-success border-0 shadow-sm py-2 px-3 mb-3 d-flex align-items-center">
        <span class="mdi mdi-check-circle mr-2" style="font-size: 1.2rem;"></span>
        <div style="font-size:0.85rem; font-weight:600;">Importación completada con éxito. Los datos ya están disponibles en tiempo real.</div>
    </div>
<?php endif; ?>

<!-- Barra de Búsqueda -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-3 px-4">
        <form method="GET" action="<?php echo url('empresa/index'); ?>" id="formBuscar" class="d-flex align-items-center" style="gap:12px;">
            <div class="flex-grow-1">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0" style="border-radius:8px 0 0 8px;">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                    </div>
                    <input 
                        type="text" 
                        name="buscar" 
                        id="inputBuscar"
                        class="form-control border-left-0" 
                        placeholder="Buscar por nombre, departamento o ciudad..." 
                        value="<?php echo htmlspecialchars($buscar); ?>"
                        style="border-radius:0 8px 8px 0; border-left:0; padding-left:0;"
                        autocomplete="off"
                    >
                </div>
            </div>
            <?php if (!empty($buscar)): ?>
                <a href="<?php echo url('empresa/index'); ?>" class="btn btn-outline-secondary" style="border-radius:8px;">
                    <i class="bi bi-x-circle"></i> Limpiar
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
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
                                <span class="mdi mdi-domain-off" style="font-size:2.5rem;display:block;margin-bottom:10px;color:#cbd5e1;"></span>
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
                                <td><small class="text-muted"><?= htmlspecialchars($e->dpto) ?></small></td>
                                <td><small class="text-muted"><?= htmlspecialchars($e->ciudad) ?></small></td>
                                <td style="max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    <small class="text-muted"><?= htmlspecialchars($e->actividad_economica) ?></small>
                                </td>
                                <td><small><?= htmlspecialchars($e->correo_comercial) ?></small></td>
                                <td>
                                    <?php
                                    $etapa = strtolower($e->etapa_venta ?? 'prospectado');
                                    $labels = ['prospectado' => 'Prospectado', 'contactado' => 'Contactado', 'negociacion' => 'Negociación', 'ganado' => 'Ganado', 'perdido' => 'Perdido'];
                                    ?>
                                    <span class="badge-etapa badge-<?= $etapa ?>">
                                        <?= $labels[$etapa] ?? ucfirst($etapa) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex" style="gap:4px;">
                                        <a href="<?php echo url('contacto/index', ['empresa_id' => $e->id]); ?>"
                                            class="btn btn-sm btn-outline-info" title="Contactos" style="border-radius:6px;padding:3px 8px;">
                                            <i class="bi bi-people-fill"></i>
                                        </a>
                                        <a href="<?php echo url('trazabilidad/index', ['empresa_id' => $e->id]); ?>"
                                            class="btn btn-sm btn-outline-success" title="Trazabilidad" style="border-radius:6px;padding:3px 8px; border-color: #28a745;">
                                            <i class="bi bi-clock-history" style="color: #28a745; font-weight: bold;"></i>
                                        </a>
                                        <a href="<?php echo url('empresa/editar', ['id' => $e->id]); ?>"
                                            class="btn btn-sm btn-outline-primary" title="Editar" style="border-radius:6px;padding:3px 8px;">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <a href="#"
                                            class="btn btn-sm btn-outline-danger" title="Eliminar" style="border-radius:6px;padding:3px 8px;"
                                            onclick="return confirmarEliminacion('<?php echo url('empresa/eliminar', ['id' => $e->id]); ?>', '¿Eliminar la empresa <?php echo htmlspecialchars($e->razon_social); ?>?')">
                                            <i class="bi bi-trash-fill"></i>
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

<!-- JavaScript para búsqueda en tiempo real -->
<script>
// Búsqueda automática con debounce
let timeoutBusqueda = null;
const inputBuscar = document.getElementById('inputBuscar');
const formBuscar = document.getElementById('formBuscar');

if (inputBuscar) {
    inputBuscar.addEventListener('input', function() {
        clearTimeout(timeoutBusqueda);
        
        timeoutBusqueda = setTimeout(function() {
            formBuscar.submit();
        }, 500); // Esperar 500ms después de que el usuario deje de escribir
    });
    
    // Focus automático en el campo de búsqueda al cargar
    if (inputBuscar.value === '') {
        inputBuscar.focus();
    }
}

// Atajo de teclado: Ctrl/Cmd + K para enfocar búsqueda
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        inputBuscar.focus();
        inputBuscar.select();
    }
});
</script>

<?php // Footer included from BaseController 
?>