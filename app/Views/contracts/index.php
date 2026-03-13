<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regularização de Contrato - Safra</title>
    <link rel="stylesheet" href="/safra_portal_novo/public/assets/css/style.css">
    <link rel="shortcut icon" href="./assets/img/favicon.webp" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray">

    <?php include_once __DIR__ . '/../layouts/header.php'; ?>

    <main class="container">
        <div class="page-header">
            <h1>Regularização de Contrato</h1>
            <p>Verifique os detalhes do seu contrato e gere o boleto para pagamento.</p>
        </div>

        <div class="dashboard-grid">
            <div class="left-column">
                
                <div class="card contract-details">
                    <div class="card-header">
                        <h2><i class="fa-regular fa-file-lines"></i> Detalhes do Contrato</h2>
                        <span class="badge badge-warning"><i class="fa-solid fa-circle-exclamation"></i> Pagamento Pendente</span>
                    </div>
                    <div class="contract-info">
                        <div>
                            <span>Número do Contrato</span>
                            <strong><?= htmlspecialchars($contratoData['contrato'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </div>
                        <div>
                            <span>Produto</span>
                            <strong><?= htmlspecialchars($contratoData['produto'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </div>
                    </div>
                </div>

                <div class="card parcels-list">
                    <h3>Parcelas Inclusas</h3>
                    
                    <?php foreach($contratoData['parcelas'] as $parcela): ?>
                        <div class="parcel-item">
                            <div class="parcel-icon">
                                <i class="fa-regular fa-calendar"></i>
                            </div>
                            <div class="parcel-details">
                                <strong>Parcela <?= htmlspecialchars($parcela['parcela'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <span>Vencimento: <?= date('d/m/Y', strtotime($parcela['vencimento'])) ?></span>
                            </div>
                            <div class="parcel-value">
                                <strong>R$ <?= number_format($parcela['valor'], 2, ',', '.') ?></strong>
                                <span class="status <?= htmlspecialchars(strtolower($parcela['status']), ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($parcela['status'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>

            <div class="right-column">
                <div class="summary-card">
                    <div class="summary-header">
                        <span>TOTAL A PAGAR</span>
                        <h2>R$ <?= number_format($totalPagar, 2, ',', '.') ?></h2>
                    </div>
                    
                    <div class="summary-body">
                        <div class="summary-row">
                            <span>Quantidade de parcelas</span>
                            <strong><?= $qtdParcelas ?></strong>
                        </div>
                        <div class="summary-row" style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px;">
                            <span>Encargos e multas</span>
                            <strong>Calculados no total</strong>
                        </div>

                        <div id="boleto-action-container">
                            <button id="btn-gerar-boleto" class="btn-boleto" onclick="gerarBoleto('<?= htmlspecialchars($contratoData['contrato'], ENT_QUOTES, 'UTF-8') ?>')">
                                <i class="fa-solid fa-barcode"></i> Gerar Boleto de Pagamento
                            </button>

                            <div class="summary-footer">
                                <i class="fa-solid fa-dollar-sign"></i>
                                <p>O pagamento via boleto pode levar até <strong>3 dias úteis</strong> para ser compensado. Evite novos atrasos.</p>
                            </div>
                        </div>

                        <div id="boleto-success-container" style="display: none;">
                            <div class="success-box">
                                <i class="fa-solid fa-check"></i>
                                <h4>Boleto gerado com sucesso!</h4>
                                <!-- <p>Vencimento para hoje.</p> -->
                            </div>

                            <div class="barcode-section">
                                <p class="section-label">CÓDIGO DE BARRAS</p>
                                <div class="barcode-box">
                                    <p id="linha-digitavel">00000.00000 00000.000000 00000.000000 0 00000000000000</p>
                                    <button class="btn-copy" onclick="copiarCodigo()">
                                        <i class="fa-regular fa-copy"></i> Copiar Código
                                    </button>
                                </div>
                            </div>

                            <button class="btn-download-pdf">
                                <i class="fa-solid fa-download"></i> Baixar PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="/safra_portal_novo/public/assets/js/script.js"></script>
</body>
</html>