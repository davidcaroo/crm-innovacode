<?php

/**
 * Archivo de configuración para PRODUCCIÓN - Hostinger
 * Contiene todo el setup necesario e idéntico al desarrollo 
 * (excepto URLs y Credenciales DB).
 */

// Zona horaria
date_default_timezone_set("America/Bogota");

// ===============================
// CONFIGURACIÓN DE BASE DE DATOS
// ===============================
define('DB_HOST', 'localhost');
define('DB_NAME', 'u329333801_crm');
define('DB_USER', 'u329333801_crm');
define('DB_PASS', '#Dav1d.carO');
define('DB_CHARSET', 'utf8');

// Rutas del sistema
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH . '/public');
define('VIEWS_PATH', BASE_PATH . '/views');
define('MODELS_PATH', BASE_PATH . '/models');
define('CONTROLLERS_PATH', BASE_PATH . '/controllers');

// URL base dinámica para que el mismo build funcione en cualquier dominio/subdominio
$_protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$_host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('BASE_URL', $_protocol . '://' . $_host);
define('APP_BASE_PATH', ''); // En raíz del dominio
unset($_protocol, $_host);

// Configuración de la aplicación
define('APP_NAME', 'CRM Innovacode');
define('APP_VERSION', '2.0.0');

// Clave de cifrado
define('ENCRYPTION_KEY', 'tQW8bKzguz+3vMV5VP8UMF19SX0S+rFm6mya8yFz32Q=');
define('ENCRYPTION_METHOD', 'AES-256-CBC');

// Modo de desarrollo: ALWAYS false en producción
define('DEBUG_MODE', false);

// Configurar reporte de errores según el modo
if (DEBUG_MODE) {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', 1); 
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/logs/error.log');
} else {
    // ⚠️ ATENCION: si ves un 500, cambiar 'display_errors' a 1 un instante para debugearlo.
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/logs/error.log');
}

// Handler personalizado para errores PHP (opcional, para debugging)
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    $errorMsg = "[" . date('Y-m-d H:i:s') . "] PHP Error [$errno]: $errstr in $errfile on line $errline";
    error_log($errorMsg);
    if (DEBUG_MODE) {
        echo "<!-- DEBUG: $errorMsg -->";
    }
    return true;
});

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
    ini_set('session.cookie_secure', 1);  // Strict en producción
    ini_set('session.cookie_samesite', 'Strict');
}

// Limites de subida de producción
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
ini_set('max_execution_time', '300');

// Charset por defecto
header('Content-Type: text/html; charset=utf-8');

// ── Helper de URL amigables ──────────────────────────────────────────────────
/**
 * Genera una URL limpia para un par controlador/acción.
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
        // Email Marketing
        'emailMarketing/index'          => '/email-marketing',
        'emailMarketing/redactar'       => '/email-marketing/redactar',
        'emailMarketing/enviar'         => '/email-marketing/enviar',
        'emailMarketing/plantillas'     => '/email-marketing/plantillas',
        'emailMarketing/crearPlantilla' => '/email-marketing/plantillas/crear',
        'emailMarketing/guardarPlantilla' => '/email-marketing/plantillas/guardar',
        'emailMarketing/obtenerPlantillaAjax' => '/email-marketing/plantilla/ajax',
        'emailMarketing/eliminarPlantilla' => '/email-marketing/plantillas/:id/eliminar',
        // Reportes
        'reporte/index'                 => '/reportes',
        'reporte/exportarGlobalExcel'   => '/reportes/exportar-global',
        // Trazabilidad
        'trazabilidad/historial'        => '/trazabilidad',
        'trazabilidad/recordatorios'    => '/recordatorios',
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
