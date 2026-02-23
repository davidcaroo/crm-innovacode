<?php
// views/configuracion/editar.php
?>
<div class="container mt-5" style="max-width: 520px;">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4 text-center text-primary">Configuración SMTP</h4>
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
            <?php endif; ?>
            <form method="post" autocomplete="off">
                <div class="form-group">
                    <label for="smtp_host">Host SMTP</label>
                    <input type="text" class="form-control" name="smtp_host" id="smtp_host" value="<?= htmlspecialchars($smtp['smtp_host'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="smtp_port">Puerto SMTP</label>
                    <input type="number" class="form-control" name="smtp_port" id="smtp_port" value="<?= htmlspecialchars($smtp['smtp_port'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="smtp_user">Usuario SMTP</label>
                    <input type="text" class="form-control" name="smtp_user" id="smtp_user" value="<?= htmlspecialchars($smtp['smtp_user'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="smtp_pass">Contraseña SMTP</label>
                    <input type="password" class="form-control" name="smtp_pass" id="smtp_pass" value="" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block mt-3">Guardar configuración</button>
            </form>
        </div>
    </div>
</div>