<?php
namespace app\Controllers;

class AuthController {
    
    public function login() {
        require_once __DIR__ . '/../Views/auth/login.php';
    }

    public function authenticate() {
        // Nova Validação CSRF
        if (!\app\Utils\Csrf::validateToken()) {
            $_SESSION['error'] = 'Sessão expirada ou requisição inválida. Tente novamente.';
            header('Location: /safra_portal_novo/public/login');
            exit;
        }


        // Pega o documento e remove tudo que não for número (pontuações)
        $documento = preg_replace('/\D/', '', $_POST['documento'] ?? '');
        
        if (empty($documento)) {
            $_SESSION['error'] = 'Por favor, informe um documento válido.';
            header('Location: /safra_portal_novo/public/login');
            exit;
        }

        // Carrega as configurações (garantindo que o Environment foi carregado no index.php)
        $config = require __DIR__ . '/../../config/config.php';
        $apiUrl = $config['api']['base_url'];
        $apiToken = $config['api']['token'];

        // Requisição cURL para validar o cliente na API
        $ch = curl_init("{$apiUrl}/validar_cliente/{$documento}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$apiToken}",
            "Accept: application/json"
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Verifica se a API retornou sucesso (200 OK)
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            
            if (isset($data['ok']) && $data['ok'] === true) {
                // Salva o documento na sessão para usar na tela de contratos
                $_SESSION['user_doc'] = $documento;
                
                // Redireciona para contratos
                header('Location: /safra_portal_novo/public/contratos');
                exit;
            }
        }

        // Se falhou (404 ou erro da API), decodifica o erro para exibir
        $errorData = json_decode($response, true);
        $_SESSION['error'] = 'Cliente não encontrado ou sem boleto disponível.';
        
        header('Location: /safra_portal_novo/public/login');
        exit;
    }

    public function logout() {
        session_destroy();
        header('Location: /safra_portal_novo/public/login');
        exit;
    }
}