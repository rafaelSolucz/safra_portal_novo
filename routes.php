<?php
use app\Controllers\AuthController;
use app\Controllers\ContractController; // Nosso novo controller

// Rotas de Autenticação
$router->get('/', [AuthController::class, 'login']);
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'authenticate']);
$router->get('/logout', [AuthController::class, 'logout']);

// Rotas do Portal (Contratos)
$router->get('/contratos', [ContractController::class, 'index']);

// Rota para requisição de geração de boleto
$router->post('/gerar-boleto', [ContractController::class, 'gerarBoleto']);
