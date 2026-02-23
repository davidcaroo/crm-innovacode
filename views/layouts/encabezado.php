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
        <!-- Toggle Button para móviles -->
        <button class="btn btn-primary sidebar-toggle shadow-sm" id="sidebarToggle" aria-label="Abrir menú">
            <span class="mdi mdi-menu"></span>
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
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo BASE_URL; ?>/index.php?controller=empresa&action=pipeline">
                        <span class="mdi mdi-view-column"></span>
                        <span>Pipeline</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo BASE_URL; ?>/index.php?controller=empresa&action=index">
                        <span class="mdi mdi-domain"></span>
                        <span>Empresas</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo BASE_URL; ?>/index.php?controller=venta&action=index">
                        <span class="mdi mdi-store"></span>
                        <span>Ventas</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo BASE_URL; ?>/index.php?controller=dashboard&action=creditos">
                        <span class="mdi mdi-information"></span>
                        <span>Créditos</span>
                    </a>
                </li>
                <?php if (isset($_SESSION['usuario_rol']) && in_array($_SESSION['usuario_rol'], ['admin', 'superadmin'])): ?>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo BASE_URL; ?>/index.php?controller=configuracion&action=editar">
                            <span class="mdi mdi-cog"></span>
                            <span>Configuración</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo BASE_URL; ?>/index.php?controller=usuario&action=lista">
                            <span class="mdi mdi-account-group"></span>
                            <span>Usuarios</span>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo BASE_URL; ?>/index.php?controller=usuario&action=logout">
                        <span class="mdi mdi-logout"></span>
                        <span>Cerrar sesión</span>
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a class="sidebar-link text-center sidebar-cta" href="https://parzibyte.me#contacto" target="_blank">
                    <span class="mdi mdi-handshake-outline"></span>
                    <small>Ayuda y soporte</small>
                </a>
            </div>
        </nav>
        <!-- Overlay para cerrar sidebar en móviles -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        <!-- Contenido principal -->
        <?php $ctrl = isset($_GET['controller']) ? preg_replace('/[^a-z0-9_\-]/i', '', $_GET['controller']) : ''; ?>
        <main role="main" class="content-wrapper <?php echo $ctrl ? 'ctrl-' . $ctrl : ''; ?>">
        <?php else: ?>
            <main role="main" class="login-wrapper">
            <?php endif; ?>