<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<header class="main-header">
    <div class="header-content">
        <div class="header-logo-info">
            <h2 class="logo-text">SAFRA</h2>
            
            <div class="user-info">
                <p><strong>Nome:</strong> <?= $_SESSION['user_name'] ?? 'Cliente' ?></p>
                <p><strong>Documento:</strong> <?= $_SESSION['user_doc'] ?? '000.000.000-00' ?></p>
            </div>
        </div>
        
        <div class="header-actions">
            <a href="/safra_portal_novo/public/logout" class="btn-logout" title="Sair">
                <!-- <i class="fa-solid fa-arrow-right-from-bracket"></i> -->
                <!-- <i>&#10140;</i>  -->
                Sair
            </a>
        </div>
    </div>
</header>