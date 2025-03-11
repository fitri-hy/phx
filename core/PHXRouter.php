<?php
namespace Core;

use Src\Pages\NotFoundPage;

class PHXRouter {
    private static $routes = [];

    public static function get($route, $controller, $method) {
        self::registerRoute('GET', $route, $controller, $method);
    }

    public static function post($route, $controller, $method) {
        self::registerRoute('POST', $route, $controller, $method);
    }

    public static function put($route, $controller, $method) {
        self::registerRoute('PUT', $route, $controller, $method);
    }

    public static function delete($route, $controller, $method) {
        self::registerRoute('DELETE', $route, $controller, $method);
    }

    private static function registerRoute($requestMethod, $route, $controller, $method) {
        self::$routes[] = [
            'method' => $requestMethod,
            'uri' => self::sanitizeRoute($route),
            'controller' => $controller,
            'action' => $method
        ];
    }

    private static function sanitizeRoute($route) {
        return filter_var($route, FILTER_SANITIZE_URL);
    }

    public static function Route() {
        self::setCORSHeaders();

        $requestUri = strtok($_SERVER["REQUEST_URI"], '?');
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        foreach (self::$routes as $route) {
            if ($route['method'] === $requestMethod && $route['uri'] === $requestUri) {
                if (!class_exists($route['controller'])) {
                    self::showNotFound();
                }

                $controller = new $route['controller']();
                if (!method_exists($controller, $route['action'])) {
                    self::showNotFound();
                }

                if (!self::isCsrfValid($requestMethod)) {
                    self::showNotFound();
                }

                call_user_func([$controller, $route['action']]);
                exit;
            }
        }

        self::showNotFound();
    }

    private static function isCsrfValid($requestMethod) {
        if ($requestMethod === 'GET') return true;

        $headers = getallheaders();
        $csrfToken = isset($headers['X-CSRF-TOKEN']) ? $headers['X-CSRF-TOKEN'] : null;

        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $csrfToken);
    }

    private static function showNotFound() {
        http_response_code(404);
        (new NotFoundPage())->index();
        exit;
    }

    public static function setSecurityHeaders() {
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }

    public static function setCORSHeaders() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Authorization, X-CSRF-TOKEN');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}
