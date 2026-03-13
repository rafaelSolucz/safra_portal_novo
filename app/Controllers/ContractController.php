<?php
namespace app\Controllers;

class ContractController {
    
    public function index() {
        // Verifica se está logado
        if (!isset($_SESSION['user_doc'])) {
            header('Location: /safra_portal_novo/public/login');
            exit;
        }

        $documento = $_SESSION['user_doc'];
        
        $config = require __DIR__ . '/../../config/config.php';
        $apiUrl = $config['api']['base_url'];
        $apiToken = $config['api']['token'];

        // Requisição para buscar os dados do boleto
        $ch = curl_init("{$apiUrl}/dados_boleto/{$documento}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$apiToken}",
            "Accept: application/json"
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $data = json_decode($response, true);
            $dadosAPI = $data['dados'];

            // Salva o nome na sessão para o Header (se a API retornar)
            if (!empty($dadosAPI['nome'])) {
                $_SESSION['user_name'] = $dadosAPI['nome'];
            }

            // Guarda a linha digitável na sessão para a requisição via JS
            $_SESSION['linha_digitavel'] = $dadosAPI['boleto_linha_digitavel'];

            // Mapeando a resposta da API para o array esperado pela View
            // Como 'parcelas_originais' vem como string na sua API, vou mockar 
            // visualmente uma parcela única para bater com o valor total.
            $parcelas = [
                [
                    'parcela' => 'Única / Acordo', 
                    'valor' => $dadosAPI['boleto_valor_total'] ?? $dadosAPI['valor_total'] ?? 0, 
                    'vencimento' => $dadosAPI['boleto_vencimento'] ?? date('Y-m-d'), 
                    'status' => 'Pendente'
                ]
            ];

            $contratoData = [
                'contrato' => $dadosAPI['contrato_longo'] ?? $dadosAPI['contrato_curto'] ?? 'N/D',
                'produto' => $dadosAPI['credor'] ?? 'Empréstimo Safra',
                'pmt_cobrada' => $dadosAPI['pmt_cobrada'] ?? 0,
                'comissao' => $dadosAPI['comissao'] ?? 0,
                'multa' => $dadosAPI['multa'] ?? 0,
                'taxa_permanencia' => $dadosAPI['tx_perm'] ?? 0,
                'custa' => $dadosAPI['custas'] ?? 0,
                'parcelas' => $parcelas
            ];

            $totalPagar = $dadosAPI['boleto_valor_total'] ?? $dadosAPI['valor_total'] ?? 0;
            $qtdParcelas = count($contratoData['parcelas']);

            // Carrega a view passando as variáveis
            require_once __DIR__ . '/../Views/contracts/index.php';
        } else {
            // Em caso de erro na busca, desloga e manda pro início
            session_destroy();
            session_start();
            $_SESSION['error'] = "Não foi possível carregar as informações do contrato.";
            header('Location: /safra_portal_novo/public/login');
            exit;
        }
    }

    public function gerarBoleto() {
        // Simulação de delay rápido para UX
        sleep(1); 

        // Resgata a linha digitável que guardamos na sessão ao abrir a página
        $linhaDigitavel = $_SESSION['linha_digitavel'] ?? '';

        if (empty($linhaDigitavel)) {
            header('Content-Type: application/json', true, 400);
            echo json_encode(['success' => false, 'message' => 'Boleto indisponível.']);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Boleto gerado com sucesso!',
            'linha_digitavel' => $linhaDigitavel
        ]);
        exit;
    }
}