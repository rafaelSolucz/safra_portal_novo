<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Autonegociação - Safra</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="shortcut icon" href="./assets/img/favicon.webp" type="image/x-icon">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="login-container">
        <div class="login-left">
            <div class="logo">
                <!-- <h1>SAFRA</h1> -->
                <img src="./assets/img/logo-safra.png" alt="Logo Safra">
            </div>
            <h2>Acesse sua fatura pelo Portal de Autonegociação</h2>
            <p>Informe seus dados para consultar e negociar seus débitos de forma rápida e segura.</p>
        </div>

        <div class="login-right">
            <div class="login-card">
                <h3>SEJA BEM-VINDO(A)</h3>

                <?php if(isset($_SESSION['error'])): ?>
                    <div style="background-color: #fef2f2; color: #b91c1c; padding: 10px; border-radius: 6px; margin-bottom: 15px; text-align: center; font-size: 0.9rem; border: 1px solid #f87171;">
                        <?= $_SESSION['error']; ?>
                    </div>
                    <?php unset($_SESSION['error']); // Limpa o erro após exibir ?>
                <?php endif; ?>
                
                <div class="toggle-buttons">
                    <button type="button" class="btn-toggle active" id="btn-cpf" onclick="toggleDocType('cpf')">CPF</button>
                    <button type="button" class="btn-toggle" id="btn-cnpj" onclick="toggleDocType('cnpj')">CNPJ</button>
                </div>

                <form action="/safra_portal_novo/public/login" method="POST">
                    <div class="input-group">
                        <label id="label-doc" for="documento">CPF</label>
                        <input type="text" id="documento" name="documento" placeholder="Digite seu CPF" required>
                        <div id="doc-error" style="color: #d32f2f; font-size: 14px; margin-top: 5px; text-align: left; font-weight: 500;"></div>
                        <!-- <div id="doc-error" style="color: #dc2626; font-size: 0.85rem; margin-top: 4px; display: none; font-weight: 500;"></div> -->
                    </div>


                    <button type="submit" class="btn-submit">Acessar</button>
                </form>
            </div>
            <div class="contact-box">
                <p>Dúvidas? Entre em contato:</p>

                <div class="contact-buttons">

                    <a href="https://wa.me/551135097752?text=Ol%C3%A1%21%20Vim%20do%20portal%20Banco%20Safra%20x%20Araúz%20e%20gostaria%20de%20mais%20informa%C3%A7%C3%B5es."
                        class="contact-btn whatsapp" target="_blank">
                        
                        <i class="fa-brands fa-whatsapp"></i>
                       <title>WhatsApp</title>
                    </a>

                    <a href="tel:08001111515" class="contact-btn phone" target="_blank">
                        <i class="fa-solid fa-phone"></i>
                        <title>Telefone</title>
                    </a>

                    <a href="mailto:negocie.safra@arauz.com.br" class="contact-btn email" target="_blank">
                        <i class="fa-solid fa-envelope"></i>
                        <title>Email</title>
                    </a>

                </div>
            </div>
        </div>
    </div>

    <script src="./assets/js/script.js"></script>
    
</body>
</html>