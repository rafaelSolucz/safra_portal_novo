<?php
namespace app\Core;

class Request {
    public function getPath() {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        
        // Remove a query string (o que vem depois do ?)
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }

        // Descobre qual é a pasta base do projeto (ex: /safra_portal/public)
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        
        // Se a base da URL for a pasta do projeto, nós a removemos da string
        if (strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }

        // Se o resultado ficar vazio, significa que estamos na raiz (/)
        if (empty($path)) {
            $path = '/';
        }

        return $path;
    }

    public function getMethod() {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
}