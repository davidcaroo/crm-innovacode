<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo defined('APP_NAME') ? APP_NAME : 'CRM'; ?> - Nueva contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="<?php echo BASE_URL; ?>/public/css/sb-admin-custom.css" rel="stylesheet">
    <style>
        .strength-bar {
            height: 4px;
            border-radius: 2px;
            transition: width .3s, background .3s;
            width: 0;
        }
    </style>
</head>

<body class="crm-login-bg">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-body p-4">

                                    <div class="text-center mb-4">
                                        <i class="fa-solid fa-shield-halved fa-2x text-primary"></i>
                                        <h4 class="mt-2 mb-0 crm-login-brand">Nueva contraseña</h4>
                                        <?php if ($usuario): ?>
                                            <small class="text-muted">Hola, <strong><?= htmlspecialchars($usuario->nombre) ?></strong>. Elige una contraseña segura.</small>
                                        <?php else: ?>
                                            <small class="text-muted">Restablece el acceso a tu cuenta</small>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!empty($error)): ?>
                                        <div class="alert alert-danger mb-3" role="alert">
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                            <?= htmlspecialchars($error) ?>
                                            <?php if (!$usuario): ?>
                                                &nbsp;<a href="<?php echo url('usuario/recuperar'); ?>" class="alert-link">Solicitar nuevo enlace</a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($usuario): ?>
                                        <form method="post" autocomplete="off" id="formReset">
                                            <input type="hidden" name="token" value="<?= htmlspecialchars($tokenPlano) ?>">

                                            <div class="mb-3">
                                                <label for="password" class="crm-form-label">Nueva contraseña</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" name="password" id="password"
                                                        required minlength="8" placeholder="Mínimo 8 caracteres"
                                                        oninput="updateStrength(this.value)">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('password',this)" tabindex="-1" aria-label="Mostrar u ocultar contraseña">
                                                        <i class="fa-regular fa-eye"></i>
                                                    </button>
                                                </div>
                                                <div class="mt-1" style="background:#e4e8f0;border-radius:2px;height:4px;">
                                                    <div class="strength-bar" id="strengthBar"></div>
                                                </div>
                                                <small id="strengthLabel" class="text-muted" style="font-size:0.78rem;"></small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="password_confirma" class="crm-form-label">Confirmar contraseña</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" name="password_confirma" id="password_confirma"
                                                        required minlength="8" placeholder="Repite la contraseña">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('password_confirma',this)" tabindex="-1" aria-label="Mostrar u ocultar confirmación">
                                                        <i class="fa-regular fa-eye"></i>
                                                    </button>
                                                </div>
                                                <small id="matchLabel" style="font-size:0.78rem;"></small>
                                            </div>

                                            <button type="submit" class="btn btn-primary w-100 mt-1">
                                                <i class="fa-solid fa-circle-check me-1"></i> Guardar nueva contraseña
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <div class="text-center mt-3">
                                        <a href="<?php echo url('usuario/login'); ?>"
                                            class="small text-muted">
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
    <script>
        function togglePwd(id, btn) {
            var input = document.getElementById(id);
            var icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fa-regular fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fa-regular fa-eye';
            }
        }

        function updateStrength(val) {
            var bar = document.getElementById('strengthBar');
            var label = document.getElementById('strengthLabel');
            var score = 0;
            if (val.length >= 8) score++;
            if (val.length >= 12) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            var levels = [{
                    w: '20%',
                    bg: '#ef4444',
                    text: 'Muy débil'
                },
                {
                    w: '40%',
                    bg: '#f97316',
                    text: 'Débil'
                },
                {
                    w: '60%',
                    bg: '#eab308',
                    text: 'Aceptable'
                },
                {
                    w: '80%',
                    bg: '#22c55e',
                    text: 'Buena'
                },
                {
                    w: '100%',
                    bg: '#16a34a',
                    text: 'Excelente'
                }
            ];
            var lv = levels[Math.min(score, 4)];
            bar.style.width = val.length ? lv.w : '0';
            bar.style.background = lv.bg;
            label.textContent = val.length ? lv.text : '';
            label.style.color = lv.bg;
            checkMatch();
        }

        function checkMatch() {
            var p1 = document.getElementById('password').value;
            var p2 = document.getElementById('password_confirma').value;
            var label = document.getElementById('matchLabel');
            if (!p2) {
                label.textContent = '';
                return;
            }
            if (p1 === p2) {
                label.textContent = ' Las contraseñas coinciden';
                label.style.color = '#16a34a';
            } else {
                label.textContent = ' No coinciden';
                label.style.color = '#ef4444';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var p2 = document.getElementById('password_confirma');
            if (p2) p2.addEventListener('input', checkMatch);
        });
    </script>
</body>

</html>