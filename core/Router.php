<?php
/**
 * Router Class
 * Handles URL routing
 */

class Router
{
    private array $routes = [];
    private array $params = [];

    /**
     * Add a route
     */
    public function add(string $route, string $controller, string $action): void
    {
        // Convert route parameters to regex
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<$1>[^/]+)', $route);
        $route = '/^' . str_replace('/', '\/', $route) . '$/i';

        $this->routes[$route] = [
            'controller' => $controller,
            'action' => $action
        ];
    }

    /**
     * Match URL to route
     */
    public function match(string $url): bool
    {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    /**
     * Dispatch the request
     */
    public function dispatch(string $url): void
    {
        if ($this->match($url)) {
            $controller = $this->params['controller'];

            if (class_exists($controller)) {
                $controllerObj = new $controller();
                $action = $this->params['action'];

                if (method_exists($controllerObj, $action)) {
                    // Extract route parameters
                    $routeParams = array_filter($this->params, function($key) {
                        return !in_array($key, ['controller', 'action']);
                    }, ARRAY_FILTER_USE_KEY);

                    call_user_func_array([$controllerObj, $action], $routeParams);
                } else {
                    $this->error404("Method {$action} not found in {$controller}");
                }
            } else {
                $this->error404("Controller {$controller} not found");
            }
        } else {
            $this->error404("Route not found: {$url}");
        }
    }

    /**
     * Get route parameters
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Show 404 error page
     */
    private function error404(string $message = ''): void
    {
        http_response_code(404);
        include VIEW_PATH . '/errors/404.php';
        exit;
    }

    /**
     * Redirect to URL
     */
    public static function redirect(string $url): void
    {
        if (strpos($url, 'http') !== 0) {
            $url = SITE_URL . '/' . ltrim($url, '/');
        }
        header("Location: {$url}");
        exit;
    }

    /**
     * Get current URL
     */
    public static function currentUrl(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }
}
