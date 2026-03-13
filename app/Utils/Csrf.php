<?php
namespace app\Utils;

class Csrf
{
    /**
     * Obtém o token CSRF da sessão. Se não existir, gera um novo.
     */
    public static function getToken(): string
    {
        // O session_start() já é garantido pelo public/index.php, 
        // mas a verificação garante segurança extra
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Valida o token CSRF.
     * Suporta formulários tradicionais, JSON Payload e Headers HTTP.
     * @return bool Retorna true se válido, false se inválido.
     */
    public static function validateToken(): bool
    {
        $tokenEnviado = null;

        // 1. Tenta pegar do formulário tradicional POST
        if (!empty($_POST['csrf_token'])) {
            $tokenEnviado = $_POST['csrf_token'];
        } 
        // 2. Tenta pegar do Header HTTP (Padrão para Fetch/AJAX)
        elseif (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            $tokenEnviado = $_SERVER['HTTP_X_CSRF_TOKEN'];
        }
        // 3. Tenta pegar de um payload JSON (Fallback para APIs)
        elseif (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            $json = json_decode(file_get_contents('php://input'), true);
            $tokenEnviado = $json['csrf_token'] ?? null;
        }

        // Validação usando hash_equals para evitar ataques de timing
        if (empty($tokenEnviado) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $tokenEnviado)) {
            return false;
        }
        
        return true;
    }

    /**
     * Gera o campo de input oculto para formulários HTML.
     */
    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . self::getToken() . '">';
    }
}