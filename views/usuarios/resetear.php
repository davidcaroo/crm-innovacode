<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo defined('APP_NAME') ? APP_NAME : 'CRM'; ?> &ndash; Nueva contraseña</title>
    <link href="<?php echo BASE_URL; ?>/public/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.4.55/materialdesignicons.min.css">
    <link href="<?php echo BASE_URL; ?>/public/estilo.css" rel="stylesheet">
    <style>
        .strength-bar { height:4px; border-radius:2px; transition:width .3s,background .3s; width:0; }
    </style>
</head>
<body style="background:#f0f2f8;">
    <div class="login-wrapper">
        <div class="card shadow w-100" style="max-width:440px;border-radius:16px;border:1px solid #e4e8f0;">
            <div class="card-body p-4">

                <div class="text-center mb-4">
                    <span class="mdi mdi-shield-key-outline" style="font-size:2.6rem;color:#2563eb;"></span>
                    <h4 class="mt-2 mb-0" style="color:#1e40af;font-weight:700;">Nueva contraseña</h4>
                    <?php if ($usuario): ?>
                        <small class="text-muted">Hola, <strong><?= htmlspecialchars($usuario->nombre) ?></strong>. Elige una contraseña segura.</small>
                    <?php else: ?>
                        <small class="text-muted">Restablece el acceso a tu cuenta</small>
                    <?php endif; ?>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:0.9rem;border-radius:8px;">
                        <span class="mdi mdi-alert-circle-outline"></span>
                        <?= htmlspecialchars($error) ?>
                        <?php if (!$usuario): ?>
                            &nbsp;<a href="<?php echo BASE_URL; ?>/index.php?controller=usuario&action=recuperar" class="alert-link">Solicitar nuevo enlace</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($usuario): ?>
                <form method="post" autocomplete="off" id="formReset">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($tokenPlano) ?>">

                    <div class="form-group mb-3">
                        <label for="password" style="font-size:0.88rem;font-weight:600;color:#475569;">Nueva contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password" id="password"
                                required minlength="8" placeholder="Mínimo 8 caracteres"
                                oninput="updateStrength(this.value)">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('password',this)" tabindex="-1">
                                    <i class="bi bi-eye" id="eyeIcon1"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mt-1" style="background:#e4e8f0;border-radius:2px;height:4px;">
                            <div class="strength-bar" id="strengthBar"></div>
                        </div>
                        <small id="strengthLabel" class="text-muted" style="font-size:0.78rem;"></small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="password_confirma" style="font-size:0.88rem;font-weight:600;color:#475569;">Confirmar contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password_confirma" id="password_confirma"
                                required minlength="8" placeholder="Repite la contraseña">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('password_confirma',this)" tabindex="-1">
                                    <i class="bi bi-eye" id="eyeIcon2"></i>
                                </button>
                            </div>
                        </div>
                        <small id="matchLabel" style="font-size:0.78rem;"></small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block mt-1"
                        style="padding:10px;font-weight:600;letter-spacing:0.3px;border-radius:8px;">
                        <span class="mdi mdi-check-circle-outline"></span> Guardar nueva contraseña
                    </button>
                </form>
                <?php endif; ?>

                <div class="text-center mt-3">
                    <a href="<?php echo BASE_URL; ?>/index.php?controller=usuario&action=login"
                        class="text-muted" style="font-size:0.88rem;">
                        <i class="bi bi-arrow-left"></i> Volver al inicio de sesión
                    </a>
                </div>

            </div>
            <div class="card-footer text-center text-muted py-2"
                style="border-radius:0 0 16px 16px;background:#f8fafc;font-size:0.82rem;">
                CRM By Innovacode Tech &copy; <?php echo date('Y'); ?>
            </div>
        </div>
    </div>

<script>
function togglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

function updateStrength(val) {
    var bar   = document.getElementById('strengthBar');
    var label = document.getElementById('strengthLabel');
    var score = 0;
    if (val.length >= 8)  score++;
    if (val.length >= 12) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    var levels = [
        { w:'20%',  bg:'#ef4444', text:'Muy débil' },
        { w:'40%',  bg:'#f97316', text:'Débil' },
        { w:'60%',  bg:'#eab308', text:'Aceptable' },
        { w:'80%',  bg:'#22c55e', text:'Buena' },
        { w:'100%', bg:'#16a34a', text:'Excelente' }
    ];
    var lv = levels[Math.min(score, 4)];
    bar.style.width      = val.length ? lv.w : '0';
    bar.style.background = lv.bg;
    label.textContent    = val.length ? lv.text : '';
    label.style.color    = lv.bg;

    // check match
    checkMatch();
}

function checkMatch() {
    var p1    = document.getElementById('password').value;
    var p2    = document.getElementById('password_confirma').value;
    var label = document.getElementById('matchLabel');
    if (!p2) { label.textContent = ''; return; }
    if (p1 === p2) {
        label.textContent = ' Las contraseñas coinciden';
        label.style.color = '#16a34a';
    } else {
        label.textContent = ' No coinciden';
        label.style.color = '#ef4444';
    }
}

// Bind confirm field
document.addEventListener('DOMContentLoaded', function() {
    var p2 = document.getElementById('password_confirma');
    if (p2) p2.addEventListener('input', checkMatch);
});
</script>
</body>
</html>