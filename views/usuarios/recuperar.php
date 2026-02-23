<?php
// views/usuarios/recuperar.php
?>
<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card shadow-sm w-100" style="max-width: 420px;">
        <div class="card-body">
            <h4 class="mb-4 text-center text-primary">Recuperar contraseña</h4>
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-info"><?= htmlspecialchars($mensaje) ?></div>
            <?php endif; ?>
            <form method="post" autocomplete="off">
                <div class="form-group">
                    <label for="email">Correo registrado</label>
                    <input type="email" class="form-control" name="email" id="email" required autofocus>
                </div>
                <button type="submit" class="btn btn-primary btn-block mt-3">Enviar enlace de recuperación</button>
            </form>
        </div>
        <div class="card-footer text-center text-muted small">
            CRM By Innovacode Tech
        </div>
    </div>
</div>