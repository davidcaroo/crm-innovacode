<?php

/**
 * Front Controller / Router Principal
 * 
 * Este archivo es el punto de entrada único de la aplicación.
 * Recibe todas las peticiones y las dirige al controlador y acción correspondiente.
 * 
 * Patrón implementado: Front Controller
 * Arquitectura: MVC (Model-View-Controller)
 * 
 * Uso: index.php?controller=nombre&action=metodo&params
 * Ejemplo: index.php?controller=cliente&action=editar&id=5
 */
session_start();

// Cargar configuración
require_once __DIR__ . '/config/config.php';

// Control de acceso: solo login y registro públicos
$controllerName = isset($_GET['controller']) ? $_GET['controller'] : 'dashboard';
$actionName = isset($_GET['action']) ? $_GET['action'] : 'index';

if (!isset($_SESSION['usuario_id']) && !($controllerName === 'usuario' && $actionName === 'login')) {
        header('Location: ' . BASE_URL . '/index.php?controller=usuario&action=login');
        exit;
}

// Obtener parámetros de la URL
$controllerName = isset($_GET['controller']) ? $_GET['controller'] : 'dashboard';
$actionName = isset($_GET['action']) ? $_GET['action'] : 'index';

// Convertir nombre del controlador a formato de clase (PascalCase)
// Ejemplo: cliente => ClienteController
$controllerClass = ucfirst(strtolower($controllerName)) . 'Controller';

// Ruta del archivo del controlador
$controllerFile = CONTROLLERS_PATH . '/' . $controllerClass . '.php';

// Verificar que el archivo del controlador exista
if (!file_exists($controllerFile)) {
        http_response_code(404);
        die("
        <h1>Error 404</h1>
        <p>Controlador no encontrado: <strong>{$controllerClass}</strong></p>
        <p>Archivo esperado: {$controllerFile}</p>
        <a href='" . BASE_URL . "'>Volver al inicio</a>
    ");
}

// Incluir el archivo del controlador
require_once $controllerFile;

// Verificar que la clase del controlador exista
if (!class_exists($controllerClass)) {
        http_response_code(500);
        die("
        <h1>Error 500</h1>
        <p>La clase <strong>{$controllerClass}</strong> no existe en el archivo.</p>
        <a href='" . BASE_URL . "'>Volver al inicio</a>
    ");
}

// Crear instancia del controlador
try {
        $controller = new $controllerClass();

        // Verificar que el método (acción) exista en el controlador
        if (!method_exists($controller, $actionName)) {
                http_response_code(404);
                die("
            <h1>Error 404</h1>
            <p>La acción <strong>{$actionName}</strong> no existe en el controlador <strong>{$controllerClass}</strong></p>
            <p>Métodos disponibles: " . implode(', ', get_class_methods($controller)) . "</p>
            <a href='" . BASE_URL . "'>Volver al inicio</a>
        ");
        }

        // Ejecutar la acción del controlador
        $controller->$actionName();
} catch (PDOException $e) {
        // Error de base de datos
        http_response_code(500);

        if (DEBUG_MODE) {
                die("
            <h1>Error de Base de Datos</h1>
            <p><strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
            <p><strong>Archivo:</strong> " . htmlspecialchars($e->getFile()) . "</p>
            <p><strong>Línea:</strong> " . $e->getLine() . "</p>
            <pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>
            <a href='" . BASE_URL . "'>Volver al inicio</a>
        ");
        } else {
                error_log("Database Error: " . $e->getMessage());
                die("
            <h1>Error del Sistema</h1>
            <p>Ha ocurrido un error al procesar su solicitud. Por favor, intente nuevamente más tarde.</p>
            <a href='" . BASE_URL . "'>Volver al inicio</a>
        ");
        }
} catch (Exception $e) {
        // Cualquier otro error
        http_response_code(500);

        if (DEBUG_MODE) {
                die("
            <h1>Error de Aplicación</h1>
            <p><strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
            <p><strong>Archivo:</strong> " . htmlspecialchars($e->getFile()) . "</p>
            <p><strong>Línea:</strong> " . $e->getLine() . "</p>
            <pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>
            <a href='" . BASE_URL . "'>Volver al inicio</a>
        ");
        } else {
                error_log("Application Error: " . $e->getMessage());
                die("
            <h1>Error del Sistema</h1>
            <p>Ha ocurrido un error al procesar su solicitud. Por favor, intente nuevamente más tarde.</p>
            <a href='" . BASE_URL . "'>Volver al inicio</a>
        ");
        }
}
