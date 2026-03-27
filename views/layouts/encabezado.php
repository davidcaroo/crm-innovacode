<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php if (isset($_SESSION['usuario_id'])): ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/gh/StartBootstrap/startbootstrap-sb-admin-2@gh-pages/css/sb-admin-2.min.css" rel="stylesheet" crossorigin="anonymous">
        <link href="<?php echo BASE_URL; ?>/public/css/sb-admin2-crm.css?v=20260327d" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" crossorigin="anonymous">
    <?php else: ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
        <link href="<?php echo BASE_URL; ?>/public/estilo.css?v=20260327c" rel="stylesheet">
        <link href="<?php echo BASE_URL; ?>/public/css/sb-admin-custom.css?v=20260327c" rel="stylesheet">
    <?php endif; ?>

    <meta name="description" content="<?php echo APP_NAME; ?>">
    <title><?php echo APP_NAME; ?></title>
</head>

<body id="page-top">
    <?php if (isset($_SESSION['usuario_id'])): ?>
        <?php
        require_once MODELS_PATH . '/Notificacion.php';
        $notifModel    = new Notificacion();
        $noLeidas      = $notifModel->getNoLeidas($_SESSION['usuario_id']);
        $countNoLeidas = count($noLeidas);
        $iconosTipoNav = [
            'venta_ganada'     => ['icono' => 'fas fa-dollar-sign',  'color' => 'text-success'],
            'empresa_creada'   => ['icono' => 'fas fa-building',      'color' => 'text-primary'],
            'cambio_etapa'     => ['icono' => 'fas fa-random',        'color' => 'text-info'],
            'credito_aprobado' => ['icono' => 'fas fa-credit-card',   'color' => 'text-warning'],
        ];
        $ultimas5 = array_slice($noLeidas, 0, 5);
        $currentPath = strtolower((string)(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? ''));
        $activeDashboard = preg_match('#/(dashboard|)$#', $currentPath) || strpos($currentPath, '/dashboard') !== false;
        $activePipeline = strpos($currentPath, '/empresa/pipeline') !== false || strpos($currentPath, '/empresas/pipeline') !== false;
        $activeEmpresas = strpos($currentPath, '/empresa/index') !== false || strpos($currentPath, '/empresas') !== false;
        $activeVentas = strpos($currentPath, '/venta/index') !== false;
        $activeReportes = strpos($currentPath, '/reporte/index') !== false;
        $activeTrazabilidad = strpos($currentPath, '/trazabilidad') !== false;
        $activeNotificaciones = strpos($currentPath, '/notificacion') !== false;
        $activeUsuarios = strpos($currentPath, '/usuario/lista') !== false;
        $activeConfiguracion = strpos($currentPath, '/configuracion') !== false;
        $activeSoporte = strpos($currentPath, '/soporte/index') !== false;
        ?>

        <div id="wrapper">
            <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo url('dashboard/index'); ?>">
                    <div class="sidebar-brand-icon rotate-n-15">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="sidebar-brand-text mx-3"><?php echo APP_NAME; ?></div>
                </a>

                <hr class="sidebar-divider my-0">

                <li class="nav-item <?php echo $activeDashboard ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?php echo url('dashboard/index'); ?>">
                        <i class="fas fa-tachometer-alt fa-fw mr-2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <hr class="sidebar-divider">

                <div class="sidebar-heading">Gestión</div>

                <li class="nav-item <?php echo $activePipeline ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?php echo url('empresa/pipeline'); ?>">
                        <i class="fas fa-columns fa-fw mr-2"></i>
                        <span>Pipeline Comercial</span>
                    </a>
                </li>

                <li class="nav-item <?php echo $activeEmpresas ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?php echo url('empresa/index'); ?>">
                        <i class="fas fa-building fa-fw mr-2"></i>
                        <span>Mis Empresas</span>
                    </a>
                </li>

                <li class="nav-item <?php echo $activeVentas ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?php echo url('venta/index'); ?>">
                        <i class="fas fa-dollar-sign fa-fw mr-2"></i>
                        <span>Cierre de Ventas</span>
                    </a>
                </li>

                <li class="nav-item <?php echo $activeReportes ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?php echo url('reporte/index'); ?>">
                        <i class="fas fa-chart-line fa-fw mr-2"></i>
                        <span>Panel de Reportes</span>
                    </a>
                </li>

                <li class="nav-item <?php echo $activeTrazabilidad ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?php echo url('trazabilidad/historial'); ?>">
                        <i class="fas fa-history fa-fw mr-2"></i>
                        <span>Bitácora de Actividad</span>
                    </a>
                </li>

                <li class="nav-item <?php echo $activeNotificaciones ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?php echo url('notificacion/index'); ?>">
                        <i class="fas fa-bell fa-fw mr-2"></i>
                        <span>Notificaciones</span>
                    </a>
                </li>

                <?php if (isset($_SESSION['usuario_rol']) && in_array($_SESSION['usuario_rol'], ['admin', 'superadmin'])): ?>
                    <hr class="sidebar-divider">
                    <div class="sidebar-heading">Administración</div>

                    <li class="nav-item <?php echo $activeUsuarios ? 'active' : ''; ?>">
                        <a class="nav-link" href="<?php echo url('usuario/lista'); ?>">
                            <i class="fas fa-users-cog fa-fw mr-2"></i>
                            <span>Gestión de Usuarios</span>
                        </a>
                    </li>
                    <li class="nav-item <?php echo $activeConfiguracion ? 'active' : ''; ?>">
                        <a class="nav-link" href="<?php echo url('configuracion/index'); ?>">
                            <i class="fas fa-cog fa-fw mr-2"></i>
                            <span>Configuración</span>
                        </a>
                    </li>
                <?php endif; ?>

                <hr class="sidebar-divider d-none d-md-block">

                <li class="nav-item <?php echo $activeSoporte ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?php echo url('soporte/index'); ?>">
                        <i class="fas fa-question-circle fa-fw mr-2"></i>
                        <span>Ayuda y soporte</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?php echo url('usuario/logout'); ?>">
                        <i class="fas fa-sign-out-alt fa-fw mr-2"></i>
                        <span>Cerrar sesión</span>
                    </a>
                </li>

                <div class="text-center d-none d-md-inline mt-2">
                    <button class="rounded-circle border-0" id="sidebarToggle"></button>
                </div>

            </ul>

            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    <nav class="navbar navbar-expand navbar-dark bg-gradient-primary topbar mb-0 static-top shadow">
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>

                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item dropdown no-arrow mx-1">
                                <a class="nav-link dropdown-toggle" href="#" id="campanaBtn" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-bell fa-fw"></i>
                                    <?php if ($countNoLeidas > 0): ?>
                                        <span id="campanaBadge" class="badge badge-danger badge-counter"><?= min($countNoLeidas, 9) ?><?= $countNoLeidas > 9 ? '+' : '' ?></span>
                                    <?php else: ?>
                                        <span id="campanaBadge" class="badge badge-danger badge-counter" style="display:none;"></span>
                                    <?php endif; ?>
                                </a>
                                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="campanaBtn">
                                    <h6 class="dropdown-header">
                                        Notificaciones
                                    </h6>
                                    <?php if (empty($ultimas5)): ?>
                                        <span class="dropdown-item text-center small text-gray-500">Sin notificaciones nuevas</span>
                                    <?php else: ?>
                                        <?php foreach ($ultimas5 as $nf):
                                            $m = $iconosTipoNav[$nf->tipo] ?? ['icono' => 'far fa-bell', 'color' => 'text-muted'];
                                            $url = !empty($nf->url_accion) ? $nf->url_accion : url('notificacion/index');
                                        ?>
                                            <a href="<?= htmlspecialchars($url) ?>" class="dropdown-item d-flex align-items-center">
                                                <div class="mr-3">
                                                    <div class="icon-circle bg-light">
                                                        <i class="<?= $m['icono'] ?> <?= $m['color'] ?>"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="small text-gray-500"><?= date('d M, H:i', strtotime($nf->creado_en)) ?></div>
                                                    <span class="font-weight-bold"><?= htmlspecialchars($nf->titulo) ?></span>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <a class="dropdown-item text-center small text-gray-500" href="<?= url('notificacion/index') ?>">Ver todas</a>
                                </div>
                            </li>

                            <div class="topbar-divider d-none d-sm-block"></div>

                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="mr-2 d-none d-lg-inline text-white small"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
                                    <span class="img-profile rounded-circle d-inline-flex align-items-center justify-content-center bg-primary text-white" style="width:2rem;height:2rem;">
                                        <?php echo strtoupper(substr($_SESSION['usuario_nombre'], 0, 1)); ?>
                                    </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                    <span class="dropdown-item-text small text-gray-600"><?php echo htmlspecialchars($_SESSION['usuario_rol']); ?></span>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?php echo url('usuario/logout'); ?>">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Cerrar sesión
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </nav>

                    <div class="container-fluid">
                        <?php if (isset($_SESSION['is_impersonating']) && $_SESSION['is_impersonating']): ?>
                            <div class="alert alert-warning d-flex justify-content-between align-items-center" role="alert">
                                <div>
                                    <strong>Modo espectador activo.</strong>
                                    Viendo el sistema como <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>.
                                </div>
                                <a href="<?php echo url('usuario/stopImpersonating'); ?>" class="btn btn-sm btn-dark">Volver a mi Admin</a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <main role="main" class="login-wrapper">
                        <?php endif; ?>