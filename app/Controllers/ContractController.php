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

            // === NOVA LÓGICA DE PARCELAS ===
            $parcelas = [];
            
            // Verifica se a chave parcelas_originais existe e não está vazia
            if (!empty($dadosAPI['parcelas_originais'])) {
                // Decodifica a string JSON que vem do banco para um array associativo do PHP
                $parcelasDecoded = json_decode($dadosAPI['parcelas_originais'], true);
                
                if (is_array($parcelasDecoded)) {
                    $hoje = new \DateTime();
                    $hoje->setTime(0, 0, 0); // Zera a hora para comparar apenas a data
                    
                    foreach ($parcelasDecoded as $p) {
                        // A data no banco vem como d/m/Y (ex: 12/12/2025). 
                        // O PHP pode se confundir com barras, então criamos um DateTime a partir do formato exato.
                        $dataVencimento = \DateTime::createFromFormat('d/m/Y', $p['dt_vencimento']);
                        $dataVencimento->setTime(0, 0, 0);
                        
                        // Define o status comparando a data de vencimento com a data de hoje
                        $status = ($dataVencimento < $hoje) ? 'Atrasada' : 'Atual';
                        
                        $parcelas[] = [
                            'parcela' => $p['parcela'], // Pega o número da parcela (ex: "9")
                            'valor' => $p['valor_total'], // Pega o valor somado
                            'vencimento' => $dataVencimento->format('Y-m-d'), // Converte para Y-m-d para a View ler corretamente
                            'status' => $status
                        ];
                    }
                }
            }

            // Fallback: Se por acaso o array de parcelas vier vazio ou der erro no JSON, 
            // mantemos o bloco antigo para não quebrar a tela.
            if (empty($parcelas)) {
                $parcelas = [
                    [
                        'parcela' => 'Única / Acordo', 
                        'valor' => $dadosAPI['boleto_valor_total'] ?? $dadosAPI['valor_total'] ?? 0, 
                        'vencimento' => $dadosAPI['boleto_vencimento'] ?? date('Y-m-d'), 
                        'status' => 'Pendente'
                    ]
                ];
            }
            // === FIM DA NOVA LÓGICA ===

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
        // Validação CSRF para a requisição AJAX
        if (!\app\Utils\Csrf::validateToken()) {
            header('Content-Type: application/json', true, 403);
            echo json_encode(['success' => false, 'message' => 'Token de segurança inválido. Recarregue a página.']);
            exit;
        }

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