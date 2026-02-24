<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo APP_NAME; ?>">
    <title><?php echo APP_NAME; ?></title>

    <!-- CSS de Bootstrap -->
    <link href="<?php echo BASE_URL; ?>/public/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.4.55/materialdesignicons.min.css">
    <!-- Estilos propios -->
    <link href="<?php echo BASE_URL; ?>/public/estilo.css" rel="stylesheet">
    <!-- layout manejado por public/estilo.css -->
</head>

<body>
    <?php if (isset($_SESSION['usuario_id'])): ?>
        <!-- Toggle Button para móviles (Fuera de navbar si es necesario) -->
        <button class="btn sidebar-toggle shadow-lg" id="sidebarToggle" aria-label="Abrir menú" style="background-color: #1e40af; color: white; border-radius: 8px; width: 45px; height: 45px; display: none; align-items: center; justify-content: center; border: none; z-index: 9999;">
            <i class="mdi mdi-menu" style="font-size: 1.8rem;"></i>
        </button>

        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h4 class="text-primary">CRM By Innovacode Tech</h4>
                <small class="text-muted">Versión 1.0</small>
            </div>
            <ul class="sidebar-menu">
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo BASE_URL; ?>/index.php?controller=dashboard&action=index">
                        <span class="mdi mdi-desktop-mac-dashboard"></span>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-header-text mt-3 mb-2 px-4" style="font-size: 0.75rem; color: #64748b; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px;">
                    Gestión Comercial
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo BASE_URL; ?>/index.php?controller=empresa&action=pipeline">
                        <span class="mdi mdi-view-column"></span>
                        <span>Pipeline Comercial</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo BASE_URL; ?>/index.php?controller=empresa&action=index">
                        <span class="mdi mdi-domain"></span>
                        <span>Mis Empresas</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo BASE_URL; ?>/index.php?controller=venta&action=index">
                        <span class="mdi mdi-store"></span>
                        <span>Cierre de Ventas</span>
                    </a>
                </li>

                <li class="sidebar-header-text mt-3 mb-2 px-4" style="font-size: 0.75rem; color: #64748b; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px;">
                    Inteligencia de Negocio
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo BASE_URL; ?>/index.php?controller=reporte&action=index">
                        <span class="mdi mdi-chart-line"></span>
                        <span>Panel de Reportes</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo BASE_URL; ?>/index.php?controller=trazabilidad&action=historial">
                        <span class="mdi mdi-clock-outline"></span>
                        <span>Bitácora de Actividad</span>
                    </a>
                </li>

                <?php if (isset($_SESSION['usuario_rol']) && in_array($_SESSION['usuario_rol'], ['admin', 'superadmin'])): ?>
                    <li class="sidebar-header-text mt-4 mb-2 px-4" style="font-size: 0.75rem; color: #64748b; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px;">
                        Configuración avanzada
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo BASE_URL; ?>/index.php?controller=usuario&action=lista">
                            <span class="mdi mdi-account-group"></span>
                            <span>Gestión de Usuarios</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo BASE_URL; ?>/index.php?controller=configuracion&action=index">
                            <span class="mdi mdi-cog"></span>
                            <span>Configuración</span>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo BASE_URL; ?>/index.php?controller=notificacion&action=index">
                        <span class="mdi mdi-bell-outline"></span>
                        <span>Notificaciones</span>
                    </a>
                </li>

                <li class="sidebar-header-text mt-4 mb-2 px-4" style="font-size: 0.75rem; color: #64748b; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px;">
                    Sistema
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo BASE_URL; ?>/index.php?controller=usuario&action=logout">
                        <span class="mdi mdi-logout"></span>
                        <span>Cerrar sesión</span>
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a class="sidebar-link text-center sidebar-cta" href="<?php echo BASE_URL; ?>/index.php?controller=soporte&action=index">
                    <span class="mdi mdi-help-circle-outline"></span>
                    <small>Ayuda y soporte</small>
                </a>
            </div>
        </nav>
        <!-- Overlay para cerrar sidebar en móviles -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        <!-- Contenido principal -->
        <?php $ctrl = isset($_GET['controller']) ? preg_replace('/[^a-z0-9_\-]/i', '', $_GET['controller']) : ''; ?>
        <main role="main" class="content-wrapper <?php echo $ctrl ? 'ctrl-' . $ctrl : ''; ?>">

            <?php if (isset($_SESSION['is_impersonating']) && $_SESSION['is_impersonating']): ?>
                <div class="alert alert-warning shadow-sm border-0 d-flex justify-content-between align-items-center mb-4" style="border-radius:12px; border-left: 5px solid #d97706 !important;">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-bounding-box mr-3" style="font-size: 1.4rem;"></i>
                        <div>
                            <div style="font-size:0.85rem; font-weight:700; color:#92400e; text-transform:uppercase; letter-spacing:0.5px;">Modo Espectador Activo</div>
                            <div style="font-size:0.95rem; color:#b45309;">Viendo el sistema como: <strong><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></strong></div>
                        </div>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/index.php?controller=usuario&action=stopImpersonating" class="btn btn-sm btn-dark px-3" style="border-radius:8px; font-weight:700;">
                        <i class="bi bi-x-circle mr-1"></i> Volver a mi Admin
                    </a>
                </div>
            <?php endif; ?>

            <!-- Top Navbar -->
            <div class="top-navbar d-flex align-items-center justify-content-between">
                <div class="navbar-left d-flex align-items-center">
                    <button class="btn d-md-none mr-3 shadow-sm" id="sidebarToggleMobile" style="border-radius: 8px; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; background-color: #1e40af; border: none; padding: 0;">
                        <i class="mdi mdi-menu" style="font-size: 1.8rem; color: #ffffff;"></i>
                    </button>
                    <h5 class="mb-0 d-none d-sm-block text-muted" style="font-weight: 500; font-size: 0.9rem;">
                        <span class="mdi mdi-calendar-check mr-1"></span>
                        <?php echo date('d M, Y'); ?>
                    </h5>
                </div>

                <div class="navbar-right d-flex align-items-center">
                    <!-- Campana de notificaciones -->
                    <?php
                        require_once MODELS_PATH . '/Notificacion.php';
                        $notifModel   = new Notificacion();
                        $noLeidas     = $notifModel->getNoLeidas($_SESSION['usuario_id']);
                        $countNoLeidas = count($noLeidas);
                    ?>
                    <div class="dropdown mr-3">
                        <button class="btn btn-light shadow-sm position-relative" id="campanaBtn" data-toggle="dropdown" aria-expanded="false"
                                style="border-radius:10px;width:42px;height:42px;border:1px solid #e2e8f0;padding:0;display:flex;align-items:center;justify-content:center;">
                            <i class="mdi mdi-bell-outline" style="font-size:1.4rem;color:#1e40af;"></i>
                            <?php if ($countNoLeidas > 0): ?>
                                <span id="campanaBadge" class="position-absolute"
                                      style="top:-4px;right:-4px;background:#ef4444;color:#fff;border-radius:50%;width:18px;height:18px;font-size:.65rem;font-weight:800;display:flex;align-items:center;justify-content:center;border:2px solid #fff;">
                                    <?= min($countNoLeidas, 9) ?><?= $countNoLeidas > 9 ? '+' : '' ?>
                                </span>
                            <?php else: ?>
                                <span id="campanaBadge" style="display:none;position:absolute;top:-4px;right:-4px;background:#ef4444;color:#fff;border-radius:50%;width:18px;height:18px;font-size:.65rem;font-weight:800;border:2px solid #fff;align-items:center;justify-content:center;"></span>
                            <?php endif; ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 p-0"
                             style="min-width:340px;border-radius:14px;overflow:hidden;margin-top:8px;">
                            <!-- Header dropdown -->
                            <div class="d-flex align-items-center justify-content-between px-4 py-3" style="background:#1e40af;color:#fff;">
                                <span style="font-weight:700;font-size:.95rem;"><i class="mdi mdi-bell-outline mr-1"></i> Notificaciones</span>
                                <?php if ($countNoLeidas > 0): ?>
                                    <span class="badge" style="background:rgba(255,255,255,.25);color:#fff;border-radius:8px;font-size:.75rem;"><?= $countNoLeidas ?> nueva<?= $countNoLeidas !== 1 ? 's' : '' ?></span>
                                <?php endif; ?>
                            </div>
                            <!-- Lista últimas 5 -->
                            <?php
                            $iconosTipoNav = [
                                'venta_ganada'     => ['icono' => 'mdi-cash-check',         'color' => '#15803d'],
                                'empresa_creada'   => ['icono' => 'mdi-domain',              'color' => '#1e40af'],
                                'cambio_etapa'     => ['icono' => 'mdi-arrow-right-circle',  'color' => '#7c3aed'],
                                'credito_aprobado' => ['icono' => 'mdi-credit-card-check',   'color' => '#b45309'],
                            ];
                            $ultimas5 = array_slice($noLeidas, 0, 5);
                            ?>
                            <?php if (empty($ultimas5)): ?>
                                <div class="text-center py-4 text-muted" style="font-size:.88rem;">
                                    <i class="mdi mdi-bell-sleep-outline d-block mb-2" style="font-size:1.8rem;color:#cbd5e1;"></i>
                                    Sin notificaciones nuevas
                                </div>
                            <?php else: ?>
                                <?php foreach ($ultimas5 as $nf):
                                    $m   = $iconosTipoNav[$nf->tipo] ?? ['icono' => 'mdi-bell', 'color' => '#64748b'];
                                    $url = !empty($nf->url_accion) ? $nf->url_accion : BASE_URL . '/index.php?controller=notificacion&action=index';
                                ?>
                                    <a href="<?= htmlspecialchars($url) ?>" class="dropdown-item d-flex align-items-start px-4 py-3" style="border-bottom:1px solid #f1f5f9;white-space:normal;">
                                        <span class="mdi <?= $m['icono'] ?> mr-3 mt-1 flex-shrink-0" style="font-size:1.2rem;color:<?= $m['color'] ?>;"></span>
                                        <div>
                                            <div style="font-weight:700;font-size:.88rem;color:#1e293b;"><?= htmlspecialchars($nf->titulo) ?></div>
                                            <div style="font-size:.75rem;color:#94a3b8;"><?= date('d M, H:i', strtotime($nf->creado_en)) ?></div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <!-- Footer -->
                            <div class="text-center py-3" style="background:#f8fafc;">
                                <a href="<?= BASE_URL ?>/index.php?controller=notificacion&action=index"
                                   style="font-weight:700;font-size:.85rem;color:#1e40af;text-decoration:none;">
                                    <i class="mdi mdi-bell-outline mr-1"></i> Ver todas las notificaciones
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="user-info-box d-flex align-items-center">
                        <div class="text-right mr-3 d-none d-md-block">
                            <div class="user-name-top"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></div>
                            <div class="user-role-top text-uppercase"><?php echo htmlspecialchars($_SESSION['usuario_rol']); ?></div>
                        </div>
                        <div class="user-avatar-top shadow-sm">
                            <?php echo strtoupper(substr($_SESSION['usuario_nombre'], 0, 1)); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <main role="main" class="login-wrapper">
            <?php endif; ?>