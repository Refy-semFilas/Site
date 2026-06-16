<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../../../loginForm.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Pagamento</title>
    <link rel="icon" href="../../../img/Logo png.png">
    <link rel="stylesheet" href="../../../src/styles/mainHeader.css">
    <style>
        body { font-family: 'Open Sans', sans-serif; background: #f5f5f5; margin: 0; }
        header .logo { width: 90px; }
        header .opcoes { padding: 8px 0; gap: 130px; }
        .container { max-width: 480px; margin: 80px auto; background: #fff; padding: 48px 32px; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); text-align: center; }
        .beta-icon { margin-bottom: 20px; }
        h1 { color: #2d2d2d; font-size: 24px; margin: 0 0 8px; }
        p { color: #999; font-size: 15px; margin: 0 0 28px; line-height: 1.6; }
        .btn-voltar { display: inline-block; background: linear-gradient(135deg, #ff6200 0%, #ff8533 100%); color: white; padding: 12px 36px; text-decoration: none; border-radius: 50px; font-weight: 700; font-size: 15px; box-shadow: 0 4px 14px rgba(255,98,0,0.3); transition: transform 0.2s, box-shadow 0.2s; border: none; cursor: pointer; }
        .btn-voltar:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255,98,0,0.4); }
    </style>
</head>
<body>
    <header>
        <div class="imgEpesquisa">
            <div class="imagemLogo">
                <img class="logo" src="../../../img/Logo png.png" alt="logo">
            </div>
            <div class="pesquisaOpcoes">
                <div class="opcoes">
                    <a href="dashboard.php">Inicio</a>
                    <a href="../../../addProductForm.html">Adicionar item</a>
                    <a href="configPagamento.php" style="border-bottom: 1px solid #073c05;">Pagamento</a>
                    <a href="conta.php" aria-label="Minha conta">
                        <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" style="cursor:pointer;">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="beta-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#ff6200" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <line x1="9" y1="9" x2="15" y2="15"/><line x1="15" y1="9" x2="9" y2="15"/>
            </svg>
        </div>
        <h1>Beta</h1>
        <p>Pagamentos online estarão disponíveis em breve! Fique ligado.</p>
        <a href="dashboard.php" class="btn-voltar">Voltar</a>
    </div>
</body>
</html>
