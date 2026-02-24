<?php // Layout included from BaseController 
?>
<div class="container" style="max-width: 500px; margin-top: 40px;">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4 text-primary">Registrar Contacto</h4>
            <form method="post" action="<?php echo url('contacto/guardar'); ?>">
                <input type="hidden" name="empresa_id" value="<?= htmlspecialchars($empresa_id) ?>">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Cargo</label>
                    <input type="text" name="cargo" class="form-control">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary btn-block mt-3">Guardar</button>
            </form>
        </div>
    </div>
</div>
<?php // Footer included from BaseController 
?>