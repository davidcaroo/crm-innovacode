<?php

/**
 * Clase BaseController
 * Controlador base con funcionalidad común para todos los controladores
 */

abstract class BaseController
{
    /**
     * Renderizar una vista
     * 
     * @param string $viewName Nombre de la vista (sin extensión .php)
     * @param array $data Datos a pasar a la vista
     */
    protected function view($viewName, $data = [])
    {
        // Extraer datos para que estén disponibles como variables en la vista
        extract($data);

        // Incluir encabezado
        // Si la vista es login o recuperar, forzar layout limpio
        $layout = true;
        if ($viewName === 'usuarios/login' || $viewName === 'usuarios/recuperar' || $viewName === 'usuarios/resetear' || $viewName === 'usuarios/cambiar_password_obligatorio') {
            $layout = false;
        } else {
            require_once VIEWS_PATH . '/layouts/encabezado.php';
        }

        // Incluir la vista específica
        $viewFile = VIEWS_PATH . '/' . $viewName . '.php';

        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            die("Vista no encontrada: {$viewName}");
        }

        // Incluir pie de página
        if ($layout) {
            require_once VIEWS_PATH . '/layouts/pie.php';
        }
    }

    /**
     * Renderizar una vista parcial (sin header ni footer)
     * 
     * @param string $viewName Nombre de la vista parcial
     * @param array $data Datos a pasar a la vista
     */
    protected function partial($viewName, $data = [])
    {
        extract($data);

        $viewFile = VIEWS_PATH . '/' . $viewName . '.php';

        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            die("Vista parcial no encontrada: {$viewName}");
        }
    }

    /**
     * Redirigir a una URL
     *
     * Acepta:
     *   - URL absoluta: "https://..."
     *   - Ruta limpia: url('empresa/index') — resultado de la función url()
     *   - Formato legacy: "index.php?controller=X&action=Y[&param=val...]"
     *     → se convierte automáticamente a URL amigable vía url()
     *
     * @param string $url URL o par controlador/acción
     */
    protected function redirect($url)
    {
        // ── Detectar formato legacy: index.php?controller=X&action=Y ────────
        if (strpos($url, 'controller=') !== false) {
            // Extraer query string (sea cual sea el formato)
            $qpos = strpos($url, '?');
            $qs   = $qpos !== false ? substr($url, $qpos + 1) : $url;
            parse_str($qs, $qp);

            if (!empty($qp['controller']) && !empty($qp['action'])) {
                $route = $qp['controller'] . '/' . $qp['action'];
                unset($qp['controller'], $qp['action']);
                $url = url($route, $qp);
                header("Location: " . $url);
                exit();
            }
        }

        // ── URL absoluta: usar tal cual ──────────────────────────────────────
        if (strpos($url, 'http') === 0) {
            header("Location: " . $url);
            exit();
        }

        // ── Ruta relativa: anteponer BASE_URL ────────────────────────────────
        $url = BASE_URL . '/' . ltrim($url, '/');
        header("Location: " . $url);
        exit();
    }

    /**
     * Retornar JSON (útil para AJAX)
     * 
     * @param mixed $data Datos a convertir a JSON
     * @param int $statusCode Código de estado HTTP
     */
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        $options = JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE;
        echo json_encode($data, $options);
        exit();
    }

    /**
     * Validar que la petición sea POST
     * 
     * @return bool True si es POST
     */
    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Validar que la petición sea GET
     * 
     * @return bool True si es GET
     */
    protected function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Obtener valor de $_POST de forma segura
     * 
     * @param string $key Clave del dato
     * @param mixed $default Valor por defecto si no existe
     * @return mixed Valor del POST o default
     */
    protected function post($key, $default = null)
    {
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    /**
     * Obtener valor de $_GET de forma segura
     * 
     * @param string $key Clave del dato
     * @param mixed $default Valor por defecto si no existe
     * @return mixed Valor del GET o default
     */
    protected function get($key, $default = null)
    {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    /**
     * Sanitizar input para evitar XSS
     * 
     * @param string $input Input a sanitizar
     * @return string Input sanitizado
     */
    protected function sanitize($input)
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validar que un parámetro ID sea válido
     * 
     * @param mixed $id ID a validar
     * @return int ID validado
     * @throws Exception Si el ID no es válido
     */
    protected function validateId($id)
    {
        if (!isset($id) || !is_numeric($id) || $id <= 0) {
            throw new Exception("ID no válido");
        }
        return (int) $id;
    }

    /**
     * Mostrar error y detener ejecución
     * 
     * @param string $message Mensaje de error
     * @param int $code Código de error HTTP
     */
    protected function error($message, $code = 400)
    {
        http_response_code($code);

        if (DEBUG_MODE) {
            die("Error: " . $message);
        } else {
            die("Ha ocurrido un error. Por favor, intente nuevamente.");
        }
    }

    /**
     * Obtener datos maestros (departamentos)
     * 
     * @return array Array de departamentos
     */
    protected function getDepartamentos()
    {
        return DEPARTAMENTOS;
    }

    /**
     * Validar CSRF token (si está implementado)
     * 
     * @return bool True si es válido
     */
    protected function validateCsrfToken()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $token = $this->post('csrf_token');

        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generar CSRF token
     * 
     * @return string Token generado
     */
    protected function generateCsrfToken()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}
