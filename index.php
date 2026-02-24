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

// ── Enrutador de URLs amigables ──────────────────────────────────────────────
// Parsea REQUEST_URI y rellena $_GET['controller'] / $_GET['action'] + params
require_once __DIR__ . '/core/Router.php';
(new Router('/crm-php.com'))->dispatch();
// ────────────────────────────────────────────────────────────────────────────

// Control de acceso: solo login y registro públicos
$controllerName = isset($_GET['controller']) ? $_GET['controller'] : 'dashboard';
$actionName = isset($_GET['action']) ? $_GET['action'] : 'index';

if (!isset($_SESSION['usuario_id']) && !(
        $controllerName === 'usuario' && in_array($actionName, ['login', 'recuperar', 'resetear'])
)) {
        header('Location: ' . url('usuario/login'));
        exit;
}

// Middleware: Forzar cambio de contraseña si es primer login
// Permitir solo las rutas relacionadas con el cambio obligatorio
if (isset($_SESSION['cambio_password_obligatorio']) && $_SESSION['cambio_password_obligatorio'] === true) {
        $rutasPermitidas = ['cambiarPasswordObligatorio', 'procesarCambioObligatorio', 'logout'];

        if ($controllerName !== 'usuario' || !in_array($actionName, $rutasPermitidas)) {
                header('Location: ' . url('usuario/cambiarPasswordObligatorio'));
                exit;
        }
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
        include __DIR__ . '/views/errors/404.php';
        exit;
}

// Incluir el archivo del controlador
require_once $controllerFile;

// Verificar que la clase del controlador exista
if (!class_exists($controllerClass)) {
        http_response_code(500);
        $exception = new Exception("La clase {$controllerClass} no fue encontrada.");
        include __DIR__ . '/views/errors/500.php';
        exit;
}

// Crear instancia del controlador
try {
        $controller = new $controllerClass();

        // Verificar que el método (acción) exista en el controlador
        if (!method_exists($controller, $actionName)) {
                http_response_code(404);
                include __DIR__ . '/views/errors/404.php';
                exit;
        }

        // Ejecutar la acción del controlador
        $controller->$actionName();
} catch (PDOException $e) {
        // Error de base de datos
        http_response_code(500);
        $exception = $e;
        include __DIR__ . '/views/errors/500.php';
        exit;
} catch (Exception $e) {
        // Cualquier otro error
        http_response_code(500);
        $exception = $e;
        include __DIR__ . '/views/errors/500.php';
        exit;
}
