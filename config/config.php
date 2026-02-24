<?php

/**
 * Archivo de configuración general del sistema
 * Contiene constantes y configuraciones globales
 */

// Zona horaria
date_default_timezone_set("America/Mexico_City");

// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'crm_bahari');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8');

// Rutas del sistema
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH . '/public');
define('VIEWS_PATH', BASE_PATH . '/views');
define('MODELS_PATH', BASE_PATH . '/models');
define('CONTROLLERS_PATH', BASE_PATH . '/controllers');

// URL base (ajustar según tu configuración)
define('BASE_URL', 'http://localhost/crm-php.com');

// Configuración de la aplicación
define('APP_NAME', 'CRM By Innovacode Tech');
define('APP_VERSION', '2.0.0');

// Clave de cifrado para secretos (API keys, contraseñas SMTP, etc.)
// ⚠️ CAMBIAR en producción por una clave de 32 bytes segura y aleatoria
define('ENCRYPTION_KEY', 'CRM_Bahari_S3cr3t_K3y_2026_32ch!!');

// Modo de desarrollo (cambiar a false en producción)
define('DEBUG_MODE', true);

// Configurar reporte de errores según el modo
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/logs/error.log');
}

// Datos maestros
define('DEPARTAMENTOS', [
    "Nuevo León",
    "Puebla",
    "CDMX",
    "Quintana Roo",
]);

// Configuración de sesión
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Cambiar a 1 si usas HTTPS
}

// Charset por defecto
header('Content-Type: text/html; charset=utf-8');

// ── Helper de URL amigables ──────────────────────────────────────────────────
/**
 * Genera una URL limpia para un par controlador/acción.
 *
 * Uso:
 *   url('empresa/index')                          → BASE_URL/empresas
 *   url('empresa/editar', ['id' => 5])            → BASE_URL/empresas/5/editar
 *   url('contacto/index', ['empresa_id' => 3])    → BASE_URL/contactos/3
 *   url('configuracion/editar', ['tab' => 'smtp'])→ BASE_URL/configuracion?tab=smtp
 *
 * Si la ruta no existe en el mapa, genera URL legacy ?controller=X&action=Y.
 */
function url(string $route, array $params = []): string
{
    static $map = [
        // Acceso
        'usuario/login'                 => '/login',
        'usuario/logout'                => '/salir',
        'usuario/recuperar'             => '/recuperar',
        'usuario/resetear'              => '/resetear',
        // Dashboard
        'dashboard/index'               => '/dashboard',
        // Empresas
        'empresa/index'                 => '/empresas',
        'empresa/pipeline'              => '/empresas/pipeline',
        'empresa/importar'              => '/empresas/importar',
        'empresa/procesarImportacion'   => '/empresas/procesar-importacion',
        'empresa/crear'                 => '/empresas/crear',
        'empresa/guardar'               => '/empresas/guardar',
        'empresa/editar'                => '/empresas/:id/editar',
        'empresa/actualizar'            => '/empresas/actualizar',
        'empresa/eliminar'              => '/empresas/:id/eliminar',
        // Contactos
        'contacto/index'                => '/contactos/:empresa_id',
        'contacto/crear'                => '/contactos/:empresa_id/crear',
        'contacto/guardar'              => '/contactos/guardar',
        'contacto/editar'               => '/contactos/:empresa_id/:id/editar',
        'contacto/actualizar'           => '/contactos/actualizar',
        'contacto/eliminar'             => '/contactos/eliminar/:id',
        // Ventas
        'venta/index'                   => '/ventas',
        'venta/guardar'                 => '/ventas/guardar',
        'venta/eliminar'                => '/ventas/:id/eliminar',
        // Reportes
        'reporte/index'                 => '/reportes',
        // Trazabilidad
        'trazabilidad/historial'        => '/trazabilidad',
        'trazabilidad/index'            => '/trazabilidad/:empresa_id',
        'trazabilidad/registrar'        => '/trazabilidad/:empresa_id/registrar',
        // Notificaciones
        'notificacion/index'            => '/notificaciones',
        'notificacion/conteo'           => '/notificaciones/conteo',
        'notificacion/marcarTodas'      => '/notificaciones/marcar-todas',
        // Usuarios
        'usuario/lista'                 => '/usuarios',
        'usuario/crearUsuario'          => '/usuarios/crear',
        'usuario/guardarUsuario'        => '/usuarios/guardar',
        'usuario/editarUsuario'         => '/usuarios/:id/editar',
        'usuario/actualizarUsuario'     => '/usuarios/actualizar',
        'usuario/eliminarUsuario'       => '/usuarios/:id/eliminar',
        'usuario/impersonate'           => '/usuarios/:id/impersonate',
        'usuario/stopImpersonating'     => '/usuarios/stop-impersonating',
        // Configuración
        'configuracion/index'           => '/configuracion',
        'configuracion/editar'          => '/configuracion',
        'configuracion/guardar'         => '/configuracion/guardar',
        'configuracion/guardarSmtp'     => '/configuracion/smtp',
        'configuracion/probarSmtp'      => '/configuracion/probar-smtp',
        'configuracion/guardarIntegracion'   => '/configuracion/integraciones',
        'configuracion/guardarNotificaciones' => '/configuracion/notificaciones',
        // Soporte
        'soporte/index'                 => '/soporte',
    ];

    if (!isset($map[$route])) {
        // Fallback legacy para rutas no mapeadas
        $parts = explode('/', $route, 2);
        $qs = http_build_query(array_merge(
            ['controller' => $parts[0], 'action' => $parts[1] ?? 'index'],
            $params
        ));
        return BASE_URL . '/index.php?' . $qs;
    }

    $path = $map[$route];

    // Reemplazar parámetros de ruta (:nombre) con sus valores
    $extra = [];
    foreach ($params as $key => $value) {
        if (strpos($path, ':' . $key) !== false) {
            $path = str_replace(':' . $key, rawurlencode((string)$value), $path);
        } else {
            $extra[$key] = $value;
        }
    }

    // Parámetros sobrantes → query string
    $qs = !empty($extra) ? '?' . http_build_query($extra) : '';

    return BASE_URL . $path . $qs;
}
