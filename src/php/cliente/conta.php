<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../loginForm.html");
    exit;
}

require "../supabaseConnection.php";

$user_id = $_SESSION['user_id'];

$result = supabaseRequest("/rest/v1/usuarios?id=eq.$user_id&select=email,username,tipo");

if (count($result['data']) === 0) {
    session_destroy();
    header("Location: ../../../loginForm.html");
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
                    <a href="../../../home.html">Inicio</a>
                    <a href="../../../desserts.html">Doces</a>
                    <a href="../../../savorySnacks.html">Salgados</a>
                    <a href="../../../beverages.html">Bebidas</a>
                    <a href="../../../shoppingCart.html" aria-label="Ir para o carrinho">
                        <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" style="cursor:pointer;">
                            <path d="M3 3h2l3.6 10.59A2 2 0 0 0 10.5 15h7.88a2 2 0 0 0 1.93-1.5L23 6H6" />
                            <circle cx="10" cy="20" r="1.7" />
                            <circle cx="18" cy="20" r="1.7" />
                        </svg>
                    </a>
                    <a href="conta.php" aria-label="Minha conta" style="border-bottom: 1px solid #073c05;">
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

    <div class="conta-container">
        <div class="conta-card">
            <div class="conta-icon">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#ff6200" stroke-width="1.5"
                    stroke-linecap="round" stroke-linejoin="round">
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
                    <span class="label">Email cadastrado:</span>
                    <span class="value"><?php echo htmlspecialchars($usuario['email']); ?></span>
                </div>
            </div>
            <a href="../logout.php" class="sair-btn">Sair da conta</a>
        </div>
    </div>
</body>
</html>
