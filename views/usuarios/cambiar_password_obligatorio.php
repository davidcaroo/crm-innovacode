<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo defined('APP_NAME') ? APP_NAME : 'CRM'; ?> &ndash; Cambio de contraseña obligatorio</title>
    <link href="<?php echo BASE_URL; ?>/public/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.4.55/materialdesignicons.min.css">
    <link href="<?php echo BASE_URL; ?>/public/estilo.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .password-main {
            flex: 1;
            padding: 15px 20px 20px;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }

        .page-header {
            margin-bottom: 14px;
        }

        .strength-bar {
            height: 3px;
            border-radius: 2px;
            transition: width .3s, background .3s;
            width: 0;
        }

        .footer {
            background: #fff;
            border-top: 1px solid #e4e8f0;
            padding: 12px 20px;
            text-align: center;
            color: #64748b;
            font-size: 0.88rem;
            margin-top: auto;
        }
    </style>
</head>

<body>
    <main class="password-main">
        <div class="page-header">
            <div>
                <h2 class="page-title"><i class="mdi mdi-lock-reset"></i> Cambio de Contraseña Obligatorio</h2>
                <span class="page-subtitle">Por seguridad, debes cambiar tu contraseña temporal</span>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="text-center mb-3">
                            <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,#3b82f6 0%,#2563eb 100%);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;box-shadow:0 4px 12px rgba(59,130,246,0.3);">
                                <i class="mdi mdi-account-key" style="font-size:1.8rem;color:#fff;"></i>
                            </div>
                            <h5 class="mb-1" style="color:#1e40af;font-weight:700;">Hola, <?= htmlspecialchars($usuario_nombre) ?></h5>
                            <p class="text-muted mb-0" style="font-size:0.88rem;">Configura tu contraseña permanente para acceder al sistema</p>
                        </div>

                        <div class="alert alert-warning mb-3" style="background-color:#fff3cd;border-left:4px solid #ffc107;border-radius:8px;">
                            <div class="d-flex align-items-start">
                                <i class="mdi mdi-shield-alert mr-2" style="font-size:1.2rem;color:#856404;"></i>
                                <div>
                                    <strong style="color:#856404;">Importante:</strong>
                                    <p class="mb-0 mt-1" style="font-size:0.85rem;color:#856404;">
                                        Utiliza la contraseña temporal que recibiste por email y crea una nueva contraseña segura.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger mb-3" style="border-radius:8px;">
                                <i class="mdi mdi-alert-circle-outline mr-1"></i>
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="<?php echo url('usuario/procesarCambioObligatorio'); ?>" autocomplete="off" id="formCambio">

                            <p style="font-size:0.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.6px;border-bottom:1px solid #e4e8f0;padding-bottom:8px;margin-bottom:12px;">
                                <i class="bi bi-key-fill"></i> Configuración de Contraseña
                            </p>

                            <div class="form-group mb-2">
                                <label style="font-size:0.82rem;font-weight:600;color:#475569;">
                                    Contraseña temporal <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-sm" name="password_actual" id="password_actual"
                                        required placeholder="La que recibiste por email" autofocus>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="togglePwd('password_actual',this)" tabindex="-1">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-2">
                                <label style="font-size:0.82rem;font-weight:600;color:#475569;">
                                    Nueva contraseña <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-sm" name="password_nueva" id="password_nueva"
                                        required minlength="8" placeholder="Mínimo 8 caracteres"
                                        oninput="updateStrength(this.value)">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="togglePwd('password_nueva',this)" tabindex="-1">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-2" style="background:#e4e8f0;border-radius:2px;height:3px;">
                                    <div class="strength-bar" id="strengthBar"></div>
                                </div>
                                <small id="strengthLabel" class="text-muted" style="font-size:0.78rem;"></small>
                            </div>

                            <div class="form-group mb-2">
                                <label style="font-size:0.82rem;font-weight:600;color:#475569;">
                                    Confirmar contraseña <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-sm" name="password_confirma" id="password_confirma"
                                        required minlength="8" placeholder="Repite tu contraseña"
                                        oninput="checkMatch()">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="togglePwd('password_confirma',this)" tabindex="-1">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <small id="matchLabel" style="font-size:0.78rem;"></small>
                            </div>

                            <div class="alert alert-info mt-3 mb-3" style="background-color:#dbeafe;border-left:4px solid #3b82f6;border-radius:8px;font-size:0.83rem;">
                                <i class="mdi mdi-information-outline mr-1"></i>
                                <strong>Recomendación:</strong> Usa mayúsculas, minúsculas, números y símbolos para mayor seguridad.
                            </div>

                            <button type="submit" class="btn btn-primary btn-block" style="border-radius:8px;font-weight:600;padding:10px;font-size:0.95rem;">
                                <i class="mdi mdi-check-circle-outline"></i> Cambiar Contraseña y Continuar
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <span>CRM By Innovacode Tech &copy; <?php echo date('Y'); ?> | <a href="<?php echo url('soporte/index'); ?>" style="color:#2563eb;text-decoration:none;">Ayuda y soporte</a></span>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script>
        function togglePwd(inputId, btn) {
            const inp = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (inp.type === 'password') {
                inp.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                inp.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
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
                label.textContent = '✓ Las contraseñas coinciden';
                label.style.color = '#22c55e';
            } else {
                label.textContent = '✗ Las contraseñas no coinciden';
                label.style.color = '#dc2626';
            }
        }

        // Prevenir retroceso con navegador
        history.pushState(null, null, location.href);
        window.onpopstate = function() {
            history.go(1);
        };
    </script>
</body>

</html>