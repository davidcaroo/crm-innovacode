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

                <?php if (isset($_SESSION['usuario_rol']) && in_array($_SESSION['usuario_rol'], ['admin', 'superadmin'])): ?>
                    <li class="sidebar-header-text mt-4 mb-2 px-4" style="font-size: 0.75rem; color: #64748b; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px;">
                        Administración
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo BASE_URL; ?>/index.php?controller=usuario&action=lista">
                            <span class="mdi mdi-account-group"></span>
                            <span>Gestión de Usuarios</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo BASE_URL; ?>/index.php?controller=configuracion&action=editar">
                            <span class="mdi mdi-cog"></span>
                            <span>Configuracion</span>
                        </a>
                    </li>
                <?php endif; ?>

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
            
            <!-- Top Navbar -->
            <div class="top-navbar d-flex align-items-center justify-content-between">
                <div class="navbar-left">
                    <button class="btn btn-link d-md-none p-0 text-dark mr-3" id="sidebarToggleMobile">
                        <i class="mdi mdi-menu" style="font-size: 1.5rem;"></i>
                    </button>
                    <h5 class="mb-0 d-none d-sm-block text-muted" style="font-weight: 500; font-size: 0.9rem;">
                        <span class="mdi mdi-calendar-check mr-1"></span>
                        <?php echo date('d M, Y'); ?>
                    </h5>
                </div>
                
                <div class="navbar-right d-flex align-items-center">
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

            <?php if (isset($_SESSION['is_impersonating']) && $_SESSION['is_impersonating']): ?>
                <div class="alert alert-warning shadow-sm border-0 d-flex justify-content-between align-items-center mb-4" style="border-radius:12px; border-left: 5px solid #d97706 !important;">
                    <div>
                        <i class="bi bi-person-bounding-box mr-2"></i>
                        Viendo como: <strong><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></strong> 
                        <span class="badge badge-warning ml-2" style="font-size:0.7rem; text-transform:uppercase;">Modo Espectador</span>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/index.php?controller=usuario&action=stopImpersonating" class="btn btn-sm btn-dark" style="border-radius:8px; font-weight:700;">
                        <i class="bi bi-x-circle"></i> Salir y volver a Admin
                    </a>
                </div>
            <?php endif; ?>
    <?php else: ?>
        <main role="main" class="login-wrapper">
    <?php endif; ?>