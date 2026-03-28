<?php

/**
 * Tabla de rutas amigables
 * 
 * Cada entrada: 'MÉTODO /uri/pattern' => ['controller', 'action']
 * Los segmentos con : son parámetros dinámicos que se inyectan en $_GET.
 */

return [
    // ── ACCESO ────────────────────────────────────────────────
    'GET  /login'                              => ['usuario', 'login'],
    'POST /login'                              => ['usuario', 'login'],
    'GET  /salir'                              => ['usuario', 'logout'],
    'POST /salir'                              => ['usuario', 'logout'],
    'GET  /recuperar'                          => ['usuario', 'recuperar'],
    'POST /recuperar'                          => ['usuario', 'recuperar'],
    'GET  /resetear'                           => ['usuario', 'resetear'],
    'POST /resetear'                           => ['usuario', 'resetear'],

    // ── DASHBOARD ────────────────────────────────────────────
    'GET  /'                                   => ['dashboard', 'index'],
    'GET  /dashboard'                          => ['dashboard', 'index'],

    // ── EMPRESAS ────────────────────────────────────────────
    'GET  /empresas'                           => ['empresa', 'index'],
    'GET  /empresas/pipeline'                  => ['empresa', 'pipeline'],
    'GET  /empresas/importar'                  => ['empresa', 'importar'],
    'POST /empresas/procesar-importacion'      => ['empresa', 'procesarImportacion'],
    'GET  /empresas/crear'                     => ['empresa', 'crear'],
    'POST /empresas/guardar'                   => ['empresa', 'guardar'],
    'GET  /empresas/:id/editar'                => ['empresa', 'editar'],
    'POST /empresas/actualizar'                => ['empresa', 'actualizar'],
    'GET  /empresas/:id/eliminar'              => ['empresa', 'eliminar'],

    // ── CONTACTOS ───────────────────────────────────────────
    'GET  /contactos/:empresa_id'              => ['contacto', 'index'],
    'GET  /contactos/:empresa_id/crear'        => ['contacto', 'crear'],
    'POST /contactos/guardar'                  => ['contacto', 'guardar'],
    'GET  /contactos/:empresa_id/:id/editar'   => ['contacto', 'editar'],
    'POST /contactos/actualizar'               => ['contacto', 'actualizar'],
    'GET  /contactos/eliminar/:id'             => ['contacto', 'eliminar'],

    // ── VENTAS ──────────────────────────────────────────────
    'GET  /ventas'                             => ['venta', 'index'],
    'POST /ventas/guardar'                     => ['venta', 'guardar'],
    'GET  /ventas/:id/eliminar'                => ['venta', 'eliminar'],

    // ── REPORTES ────────────────────────────────────────────
    'GET  /reportes'                           => ['reporte', 'index'],
    'GET  /reportes/exportar-global'           => ['reporte', 'exportarGlobalExcel'],

    // ── TRAZABILIDAD ────────────────────────────────────────
    'GET  /trazabilidad'                        => ['trazabilidad', 'historial'],
    'GET  /trazabilidad/exportar'               => ['trazabilidad', 'exportar'],
    'GET  /trazabilidad/:empresa_id'            => ['trazabilidad', 'index'],
    'GET  /trazabilidad/:empresa_id/registrar'  => ['trazabilidad', 'registrar'],
    'POST /trazabilidad/:empresa_id/registrar'  => ['trazabilidad', 'registrar'],
    'POST /trazabilidad/guardar'                => ['trazabilidad', 'registrar'],

    // ── NOTIFICACIONES ──────────────────────────────────────
    'GET  /notificaciones'                     => ['notificacion', 'index'],
    'GET  /notificaciones/conteo'              => ['notificacion', 'conteo'],
    'POST /notificaciones/marcar-todas'        => ['notificacion', 'marcarTodas'],

    // ── USUARIOS ────────────────────────────────────────────
    'GET  /usuarios'                           => ['usuario', 'lista'],
    'GET  /usuarios/crear'                     => ['usuario', 'crearUsuario'],
    'POST /usuarios/guardar'                   => ['usuario', 'guardarUsuario'],
    'GET  /usuarios/:id/editar'                => ['usuario', 'editarUsuario'],
    'POST /usuarios/actualizar'                => ['usuario', 'actualizarUsuario'],
    'GET  /usuarios/:id/eliminar'              => ['usuario', 'eliminarUsuario'],
    'GET  /usuarios/:id/impersonate'           => ['usuario', 'impersonate'],
    'GET  /usuarios/stop-impersonating'        => ['usuario', 'stopImpersonating'],

    // ── CONFIGURACIÓN ───────────────────────────────────────
    'GET  /configuracion'                      => ['configuracion', 'index'],
    'POST /configuracion/guardar'              => ['configuracion', 'guardar'],
    'POST /configuracion/smtp'                 => ['configuracion', 'guardarSmtp'],
    'POST /configuracion/probar-smtp'          => ['configuracion', 'probarSmtp'],
    'POST /configuracion/integraciones'        => ['configuracion', 'guardarIntegracion'],
    'POST /configuracion/notificaciones'       => ['configuracion', 'guardarNotificaciones'],

    // ── SOPORTE ─────────────────────────────────────────────
    'GET  /soporte'                            => ['soporte', 'index'],
];
