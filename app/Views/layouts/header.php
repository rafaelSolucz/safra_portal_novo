<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta name="csrf-token" content="<?= app\Utils\Csrf::getToken() ?>">
</head>
<header class="main-header">
    <div class="header-content">
        <div class="header-logo-info">
            <img src="./assets/img/logo-safra.png" alt="Logo Safra" class="logo-img">
            
            <div class="user-info">
                <p><strong>Nome:</strong> <?= htmlspecialchars($_SESSION['user_name'] ?? 'Cliente', ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Documento:</strong> <?= htmlspecialchars($_SESSION['user_doc'] ?? '000.000.000-00', ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>
        
        <div class="header-actions">
            <a href="/safra_portal_novo/public/logout" class="btn-logout" title="Sair">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                Sair
            </a>
        </div>
    </div>
</header>
<div class="header-divider" style="height: 3px; background: var(--secondary-color);"></div>