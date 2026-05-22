<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../../loginForm.html");
    exit;
}

require "../supabaseConnection.php";

$user_id = $_SESSION['user_id'];
$result = supabaseRequest("/rest/v1/usuarios?id=eq.$user_id&select=email,username,tipo");

if (count($result['data']) === 0) {
    session_destroy();
    header("Location: ../../loginForm.html");
    exit;
}

$usuario = $result['data'][0];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta</title>
    <link rel="icon" href="../../../img/Logo png.png">
    <link rel="stylesheet" href="../../../src/styles/mainHeader.css">
    <link rel="stylesheet" href="../../../src/styles/conta.css">
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
                    <a href="conta.php" style="border-bottom: 1px solid #073c05;">
                        <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="conta-container">
        <div class="conta-card">
            <div class="conta-icon">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#ff6200" stroke-width="1.5">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            <h2>Minha Conta</h2>
            <div class="conta-info">
                <div class="info-row">
                    <span class="label">Tipo de conta:</span>
                    <span class="value"><?php echo $usuario['tipo'] === 'admin' ? 'Cantineiro' : 'Cliente'; ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Nome de usuário:</span>
                    <span class="value"><?php echo htmlspecialchars($usuario['username']); ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Email:</span>
                    <span class="value"><?php echo htmlspecialchars($usuario['email']); ?></span>
                </div>
            </div>
            <a href="logout.php" class="sair-btn">Sair da conta</a>
        </div>
    </div>
</body>
</html>
