<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo defined('APP_NAME') ? APP_NAME : 'CRM'; ?> - Iniciar sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="<?php echo BASE_URL; ?>/public/css/sb-admin-custom.css" rel="stylesheet">
</head>

<body class="crm-login-bg">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container crm-login-container">
                    <div class="row justify-content-center">
                        <div class="col-xl-10 col-xxl-9">
                            <div class="crm-login-shell shadow-lg">
                                <div class="crm-login-hero">
                                    <div>
                                        <!-- <span class="crm-login-kicker">Panel CRM</span> -->
                                        <h1 class="crm-login-hero-title"><?php echo defined('APP_NAME') ? APP_NAME : 'CRM'; ?></h1>
                                        <p class="crm-login-hero-text mb-0">Centraliza empresas, contactos y oportunidades en una sola vista para acelerar tu operación comercial.</p>
                                    </div>
                                    <div class="crm-login-hero-art">
                                        <img src="<?php echo BASE_URL; ?>/public/img/icono-blanco.png" alt="Ilustración comercial" class="img-fluid">
                                    </div>
                                </div>

                                <div class="crm-login-panel">
                                    <div class="crm-login-panel-head text-center">
                                        <h2 class="crm-login-brand mb-1"><?php echo defined('APP_NAME') ? APP_NAME : 'CRM'; ?></h2>
                                        <p class="text-muted mb-0">Inicia sesión para continuar</p>
                                    </div>

                                    <?php if (!empty($error)): ?>
                                        <div class="alert alert-danger" role="alert">
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                            <?= htmlspecialchars($error) ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($_SESSION['flash_exito'])): ?>
                                        <div class="alert alert-success" role="alert">
                                            <i class="fa-solid fa-circle-check me-1"></i>
                                            <?= htmlspecialchars($_SESSION['flash_exito']) ?>
                                        </div>
                                        <?php unset($_SESSION['flash_exito']); ?>
                                    <?php endif; ?>

                                    <form method="post" autocomplete="off" class="crm-login-form">
                                        <div class="mb-3">
                                            <label class="form-label" for="email">Correo electrónico</label>
                                            <div class="input-group crm-login-input-group">
                                                <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                                                <input class="form-control" id="email" type="email" name="email" placeholder="name@example.com" required autofocus>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="password">Contraseña</label>
                                            <div class="input-group crm-login-input-group">
                                                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                                <input class="form-control" id="password" type="password" name="password" placeholder="Tu contraseña" required>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2 mt-4">
                                            <button class="btn btn-primary crm-login-btn" type="submit">
                                                <i class="fa-solid fa-right-to-bracket me-1"></i> Entrar al sistema
                                            </button>
                                        </div>
                                        <div class="text-center mt-3">
                                            <a class="small" href="<?php echo url('usuario/recuperar'); ?>">¿Olvidaste tu contraseña?</a>
                                        </div>
                                    </form>

                                    <div class="crm-login-footer text-center">
                                        CRM By Innovacode Tech &copy; <?php echo date('Y'); ?>
                                    </div>
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