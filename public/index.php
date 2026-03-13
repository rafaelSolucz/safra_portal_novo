<?php
session_start();

// Autoloader simples para carregar as classes da pasta app/ automaticamente
spl_autoload_register(function ($class) {
    // Converte o namespace (ex: app\Core\Router) para o caminho do arquivo (app/Core/Router.php)
    $path = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

use app\Core\Request;
use app\Core\Router;
use app\Core\Environment; // Importa a classe Environment

// Carrega as variáveis de ambiente do arquivo .env que deve estar na raiz do projeto
try {
    Environment::load(__DIR__ . '/../');
} catch (Exception $e) {
    die("Erro Crítico: " . $e->getMessage());
}

// Instancia as classes do núcleo
$request = new Request();
$router = new Router($request);

// Carrega o arquivo de rotas
require_once __DIR__ . '/../routes.php';

// Executa a rota acessada
$router->resolve();