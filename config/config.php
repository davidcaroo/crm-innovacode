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
define('APP_NAME', 'CRM Bahari');
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
