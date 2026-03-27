<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo defined('APP_NAME') ? APP_NAME : 'CRM'; ?> - Cambio obligatorio de contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="<?php echo BASE_URL; ?>/public/css/sb-admin-custom.css" rel="stylesheet">
    <style>
        .strength-bar {
            height: 4px;
            border-radius: 2px;
            width: 0;
            transition: width .3s, background .3s;
        }
    </style>
</head>

<body class="crm-login-bg">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                            <div class="card shadow-lg border-0 rounded-lg mt-5 mb-4">
                                <div class="card-header text-center py-4">
                                    <h3 class="my-0 crm-login-brand">Cambio de contraseña obligatorio</h3>
                                    <small class="text-muted">Por seguridad, debes actualizar tu contraseña temporal</small>
                                </div>

                                <div class="card-body">
                                    <div class="alert alert-warning" role="alert">
                                        <i class="fa-solid fa-shield-halved me-1"></i>
                                        Hola <strong><?= htmlspecialchars($usuario_nombre) ?></strong>, usa la contraseña temporal recibida por correo y define una nueva contraseña segura.
                                    </div>

                                    <?php if (!empty($error)): ?>
                                        <div class="alert alert-danger" role="alert">
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                            <?= htmlspecialchars($error) ?>
                                        </div>
                                    <?php endif; ?>

                                    <form method="post" action="<?php echo url('usuario/procesarCambioObligatorio'); ?>" autocomplete="off" id="formCambio">
                                        <p class="crm-section-title">
                                            <i class="fa-solid fa-key"></i> Configuración de contraseña
                                        </p>

                                        <div class="mb-3">
                                            <label for="password_actual" class="crm-form-label">Contraseña temporal <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="password_actual" id="password_actual" required placeholder="La que recibiste por email" autofocus>
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('password_actual', this)" tabindex="-1" aria-label="Mostrar contraseña temporal">
                                                    <i class="fa-regular fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="password_nueva" class="crm-form-label">Nueva contraseña <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="password_nueva" id="password_nueva" required minlength="8" placeholder="Mínimo 8 caracteres" oninput="updateStrength(this.value)">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('password_nueva', this)" tabindex="-1" aria-label="Mostrar nueva contraseña">
                                                    <i class="fa-regular fa-eye"></i>
                                                </button>
                                            </div>
                                            <div class="mt-2" style="background:#e4e8f0;border-radius:2px;height:4px;">
                                                <div class="strength-bar" id="strengthBar"></div>
                                            </div>
                                            <small id="strengthLabel" class="text-muted" style="font-size:0.78rem;"></small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="password_confirma" class="crm-form-label">Confirmar contraseña <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="password_confirma" id="password_confirma" required minlength="8" placeholder="Repite tu contraseña" oninput="checkMatch()">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('password_confirma', this)" tabindex="-1" aria-label="Mostrar confirmación">
                                                    <i class="fa-regular fa-eye"></i>
                                                </button>
                                            </div>
                                            <small id="matchLabel" style="font-size:0.78rem;"></small>
                                        </div>

                                        <div class="alert alert-info small">
                                            <i class="fa-solid fa-circle-info me-1"></i>
                                            Recomendación: combina mayúsculas, minúsculas, números y símbolos.
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fa-solid fa-circle-check me-1"></i> Cambiar contraseña y continuar
                                        </button>
                                    </form>
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
        function togglePwd(inputId, btn) {
            const inp = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (inp.type === 'password') {
                inp.type = 'text';
                icon.className = 'fa-regular fa-eye-slash';
            } else {
                inp.type = 'password';
                icon.className = 'fa-regular fa-eye';
            }
        }

        function updateStrength(password) {
            const bar = document.getElementById('strengthBar');
            const label = document.getElementById('strengthLabel');
            const len = password.length;

            if (len === 0) {
                bar.style.width = '0';
                label.textContent = '';
                return;
            }

            let strength = 0;
            if (len >= 8) strength++;
            if (len >= 12) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[@$!%*?&#]/.test(password)) strength++;

            const width = Math.min(strength * 20, 100);
            bar.style.width = width + '%';

            if (strength <= 2) {
                bar.style.background = '#dc2626';
                label.textContent = 'Débil';
                label.style.color = '#dc2626';
            } else if (strength === 3) {
                bar.style.background = '#f59e0b';
                label.textContent = 'Aceptable';
                label.style.color = '#f59e0b';
            } else if (strength === 4) {
                bar.style.background = '#3b82f6';
                label.textContent = 'Buena';
                label.style.color = '#3b82f6';
            } else {
                bar.style.background = '#22c55e';
                label.textContent = 'Excelente';
                label.style.color = '#22c55e';
            }
        }

        function checkMatch() {
            const pwd = document.getElementById('password_nueva').value;
            const confirm = document.getElementById('password_confirma').value;
            const label = document.getElementById('matchLabel');

            if (confirm.length === 0) {
                label.textContent = '';
                return;
            }

            if (pwd === confirm) {
                label.textContent = ' Las contraseñas coinciden';
                label.style.color = '#22c55e';
            } else {
                label.textContent = ' Las contraseñas no coinciden';
                label.style.color = '#dc2626';
            }
        }

        history.pushState(null, null, location.href);
        window.onpopstate = function() {
            history.go(1);
        };
    </script>
</body>

</html>