<?php
namespace app\Controllers;

class AuthController {
    
    public function login() {
        require_once __DIR__ . '/../Views/auth/login.php';
    }

    public function authenticate() {
        // Simulação: Após validar CPF/CNPJ na API, salvamos na sessão e redirecionamos
        $_SESSION['user_doc'] = $_POST['documento'] ?? 'Desconhecido';
        $_SESSION['user_name'] = 'CLIENTE TESTE SAFRA'; // Nome mockado
        
        header('Location: /safra_portal_novo/public/contratos'); // Redireciona para contratos
        exit;
    }

    public function logout() {
        session_destroy();
        header('Location: /safra_portal_novo/public/login');
        exit;
    }
}