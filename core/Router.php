<?php

/**
 * Router ligero para URLs amigables
 * 
 * Parsea REQUEST_URI, lo compara contra la tabla de rutas (config/routes.php)
 * y establece $_GET['controller'] / $_GET['action'] junto con cualquier
 * parámetro dinámico extraído del URI (:id, :empresa_id, etc.).
 * 
 * Si ninguna ruta coincide, el router respeta los valores de $_GET que ya
 * vengan en la query string (compatibilidad con el sistema legacy).
 */
class Router
{
    /** @var array Tabla de rutas cargada de config/routes.php */
    private array $routes = [];

    /** @var string Segmento base a ignorar del REQUEST_URI (ej. /crm-php.com) */
    private string $basePath;

    public function __construct(string $basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
        $this->routes   = require __DIR__ . '/../config/routes.php';
    }

    /**
     * Resuelve el REQUEST_URI actual contra la tabla de rutas.
     * Rellena $_GET con controller, action y parámetros de ruta.
     *
     * @return bool  true si se encontró una ruta limpia, false si se usa fallback legacy
     */
    public function dispatch(): bool
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));

        // Quitar el basePath del inicio
        if ($this->basePath !== '' && str_starts_with($uri, $this->basePath)) {
            $uri = substr($uri, strlen($this->basePath));
        }

        // Normalizar: siempre con / inicial, sin trailing slash (excepto raíz)
        $uri = '/' . ltrim($uri, '/');
        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        // ── Ordenar rutas: las más estáticas (sin ":") primero ──────────────
        // Esto evita que /contactos/:empresa_id capture /contactos/guardar
        $staticRoutes  = [];
        $dynamicRoutes = [];
        foreach ($this->routes as $pattern => $target) {
            if (strpos($pattern, ':') === false) {
                $staticRoutes[$pattern] = $target;
            } else {
                $dynamicRoutes[$pattern] = $target;
            }
        }
        // Ordenar dinámicas: más segmentos/más específica primero
        uksort($dynamicRoutes, function ($a, $b) {
            return substr_count($b, '/') <=> substr_count($a, '/');
        });

        $orderedRoutes = $staticRoutes + $dynamicRoutes;

        // ── Intentar cada ruta ───────────────────────────────────────────────
        foreach ($orderedRoutes as $routeKey => $target) {
            // Cada llave tiene formato "METODO  /patron"
            [$routeMethod, $routePattern] = preg_split('/\s+/', trim($routeKey), 2);

            if (strtoupper($routeMethod) !== $method) {
                continue;
            }

            $params = $this->match($routePattern, $uri);
            if ($params !== null) {
                // Inyectar controller y action en $_GET
                $_GET['controller'] = $target[0];
                $_GET['action']     = $target[1];

                // Inyectar parámetros dinámicos extraídos del URI
                foreach ($params as $key => $value) {
                    $_GET[$key] = $value;
                }

                return true;
            }
        }

        // ── Fallback legacy (?controller=X&action=Y) ────────────────────────
        // Si el URI es exactamente /index.php o / sin rutas coincidentes,
        // se respetan los valores de $_GET tal como vienen.
        return false;
    }

    /**
     * Compara un patrón de ruta contra un URI concreto.
     * Devuelve un array con los parámetros extraídos o null si no coincide.
     *
     * @param string $pattern  Ej: /empresas/:id/editar
     * @param string $uri      Ej: /empresas/5/editar
     * @return array|null
     */
    private function match(string $pattern, string $uri): ?array
    {
        // Convertir el patrón en regex:
        // :param_name   →  ([^/]+)
        $regex = preg_replace('/:([a-zA-Z_][a-zA-Z0-9_]*)/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $uri, $matches)) {
            return null;
        }

        // Extraer sólo los grupos nombrados (los parámetros de ruta)
        $params = [];
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }
}
