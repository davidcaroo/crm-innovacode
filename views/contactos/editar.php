<?php // Layout included from BaseController 
?>
<div class="container" style="max-width: 500px; margin-top: 40px;">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4 text-primary">Editar Contacto</h4>
            <form method="post" action="<?php echo url('contacto/actualizar'); ?>">
                <input type="hidden" name="id" value="<?= htmlspecialchars($contacto->id) ?>">
                <input type="hidden" name="empresa_id" value="<?= htmlspecialchars($contacto->empresa_id) ?>">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($contacto->nombre) ?>" required>
                </div>
                <div class="form-group">
                    <label>Cargo</label>
                    <input type="text" name="cargo" class="form-control" value="<?= htmlspecialchars($contacto->cargo) ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($contacto->email) ?>">
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($contacto->telefono) ?>">
                </div>
                <button type="submit" class="btn btn-primary btn-block mt-3">Actualizar</button>
            </form>
        </div>
    </div>
</div>
<?php // Footer included from BaseController 
?>