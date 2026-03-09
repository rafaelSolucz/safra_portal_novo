<?php
namespace app\Core;

class Router {
    public Request $request;
    protected array $routes = [];

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function get($path, $callback) {
        $this->routes['get'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['post'][$path] = $callback;
    }

    public function resolve() {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;

        if ($callback === false) {
            http_response_code(404);
            echo "Página não encontrada (404)";
            exit;
        }

        // Se o callback for um array [Controller::class, 'metodo']
        if (is_array($callback)) {
            $controller = new $callback[0]();
            $callback[0] = $controller;
        }

        return call_user_func($callback, $this->request);
    }
}   