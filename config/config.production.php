<?php

/**
 * Archivo de configuración para PRODUCCIÓN - Hostinger
 * 
 * INSTRUCCIONES DE INSTALACIÓN:
 * =============================
 * 1. Subir todos los archivos del CRM a Hostinger EXCEPTO:
 *    - config/config.php (el de desarrollo)
 *    - .git/ (directorio de git)
 *    - logs/*.log (archivos de log viejos)
 *    
 * 2. En el servidor de Hostinger, RENOMBRAR este archivo de:
 *    config.production.php  →  config.php
 *    
 * 3. Importar la base de datos usando phpMyAdmin en Hostinger:
 *    - Usar el archivo: esquema.sql
 *    - Base de datos: u329333801_crm_bahari
 *    
 * 4. Verificar permisos del directorio logs/ (debe tener permisos de escritura)
 * 
 * 5. Probar acceso: https://crm.bahariaqua.com
 */

// ===============================
// CONFIGURACIÓN DE BASE DE DATOS
// ===============================
define('DB_HOST', 'localhost');  // En Hostinger normalmente es 'localhost'
define('DB_NAME', 'u329333801_crm_bahari');
define('DB_USER', 'u329333801_crm_bahari');
define('DB_PASS', 'MNQ+XPV|5jR');

// ===============================
// CONFIGURACIÓN DE LA APLICACIÓN
// ===============================
define('APP_NAME', 'CRM Bahari');
define('BASE_URL', 'https://crm.bahariaqua.com');
define('APP_BASE_PATH', '');  // Vacío porque está en la raíz del dominio

// ===============================
// ZONA HORARIA
// ===============================
date_default_timezone_set('America/Bogota');

// ===============================
// SEGURIDAD Y ENCRIPTACIÓN
// ===============================
// ⚠️ CLAVE GENERADA AUTOMÁTICAMENTE - NO COMPARTIR
define('ENCRYPTION_KEY', 'tQW8bKzguz+3vMV5VP8UMF19SX0S+rFm6mya8yFz32Q=');
define('ENCRYPTION_METHOD', 'AES-256-CBC');

// ===============================
// MODO DE DEPURACIÓN
// ===============================
// ⚠️ IMPORTANTE: Siempre FALSE en producción para no mostrar errores sensibles
define('DEBUG_MODE', false);

// ===============================
// CONFIGURACIÓN DE ERRORES
// ===============================
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/error.log');
} else {
    // En producción: No mostrar errores en pantalla, solo registrarlos
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/error.log');
}

// ===============================
// CONFIGURACIÓN DE SESIONES
// ===============================
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);  // Requiere HTTPS
ini_set('session.cookie_samesite', 'Strict');

// ===============================
// LÍMITES DE SUBIDA DE ARCHIVOS
// ===============================
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
ini_set('max_execution_time', '300');

// ===============================
// VERIFICACIÓN DE CREDENCIALES
// ===============================
if (DB_HOST === 'TU_HOST_AQUI' || DB_PASS === 'TU_PASSWORD_AQUI') {
    die('ERROR: Las credenciales de la base de datos no han sido configuradas correctamente.');
}
