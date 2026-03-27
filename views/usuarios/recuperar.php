<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo defined('APP_NAME') ? APP_NAME : 'CRM'; ?> - Recuperar contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="<?php echo BASE_URL; ?>/public/css/sb-admin-custom.css" rel="stylesheet">
</head>

<body class="crm-login-bg">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header text-center py-4">
                                    <h3 class="my-0 crm-login-brand">Recuperar contraseña</h3>
                                    <small class="text-muted">Ingresa tu correo para recibir el enlace</small>
                                </div>

                                <div class="card-body">
                                    <?php if (!empty($mensaje)): ?>
                                        <?php if (($tipo ?? '') === 'exito'): ?>
                                            <div class="alert alert-success" role="alert">
                                                <i class="fa-solid fa-circle-check me-1"></i>
                                                <?= htmlspecialchars($mensaje) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-danger" role="alert">
                                                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                                <?= htmlspecialchars($mensaje) ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if (empty($mensaje)): ?>
                                        <form method="post" autocomplete="off">
                                            <div class="form-floating mb-3">
                                                <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required autofocus>
                                                <label for="email">Correo electrónico registrado</label>
                                            </div>

                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fa-solid fa-paper-plane me-1"></i> Enviar enlace de recuperación
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <div class="text-center mt-3">
                                        <a href="<?php echo url('usuario/login'); ?>" class="small text-muted">
                                            <i class="fa-solid fa-arrow-left me-1"></i> Volver al inicio de sesión
                                        </a>
                                    </div>
                                </div>

                                <div class="card-footer text-center py-3">
                                    <div class="small text-muted">CRM By Innovacode Tech &copy; <?php echo date('Y'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>