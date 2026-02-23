<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo defined('APP_NAME') ? APP_NAME : 'CRM'; ?> &ndash; Iniciar sesión</title>
    <link href="<?php echo BASE_URL; ?>/public/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.4.55/materialdesignicons.min.css">
    <link href="<?php echo BASE_URL; ?>/public/estilo.css" rel="stylesheet">
</head>

<body style="background:#f0f2f8;">

    <div class="login-wrapper">
        <div class="card shadow w-100" style="max-width:420px;border-radius:16px;border:1px solid #e4e8f0;">
            <div class="card-body p-4">

                <div class="text-center mb-4">
                    <span class="mdi mdi-domain" style="font-size:2.6rem;color:#2563eb;"></span>
                    <h4 class="mt-2 mb-0" style="color:#1e40af;font-weight:700;">
                        <?php echo defined('APP_NAME') ? APP_NAME : 'CRM'; ?>
                    </h4>
                    <small class="text-muted">Inicia sesión para continuar</small>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:0.9rem;border-radius:8px;">
                        <span class="mdi mdi-alert-circle-outline"></span>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="post" autocomplete="off">
                    <div class="form-group mb-3">
                        <label for="email" style="font-size:0.88rem;font-weight:600;color:#475569;">
                            Correo electrónico
                        </label>
                        <input type="email" class="form-control" name="email" id="email"
                            required autofocus placeholder="tucorreo@empresa.com">
                    </div>
                    <div class="form-group mb-3">
                        <label for="password" style="font-size:0.88rem;font-weight:600;color:#475569;">
                            Contraseña
                        </label>
                        <input type="password" class="form-control" name="password" id="password"
                            required placeholder="••••••••">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block mt-1"
                        style="padding:10px;font-weight:600;letter-spacing:0.3px;border-radius:8px;">
                        <span class="mdi mdi-login"></span> Entrar
                    </button>
                    <div class="text-center mt-3">
                        <a href="<?php echo BASE_URL; ?>/index.php?controller=usuario&action=recuperar"
                            class="text-muted" style="font-size:0.88rem;">¿Olvidé mi contraseña?</a>
                    </div>
                </form>

            </div>
            <div class="card-footer text-center text-muted py-2"
                style="border-radius:0 0 16px 16px;background:#f8fafc;font-size:0.82rem;">
                CRM By Innovacode Tech &copy; <?php echo date('Y'); ?>
            </div>
        </div>
    </div>

</body>

</html>