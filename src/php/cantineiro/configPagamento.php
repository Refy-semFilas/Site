<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../../../loginForm.html");
    exit;
}

require "../supabaseConnection.php";

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = trim($_POST['mp_token'] ?? '');
    $update = supabaseRequest("/rest/v1/usuarios?id=eq.$user_id", 'PATCH', [
        'mp_access_token' => $token
    ]);

    if ($update['code'] === 200 || $update['code'] === 201 || $update['code'] === 204) {
        echo json_encode(['success' => true, 'message' => 'Token salvo com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar token.']);
    }
    exit;
}

$result = supabaseRequest("/rest/v1/usuarios?id=eq.$user_id&select=mp_access_token,username");
$tokenAtual = $result['data'][0]['mp_access_token'] ?? '';
$username = $result['data'][0]['username'] ?? '';
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
        header .opcoes { padding: 8px 0; gap: 80px; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h1 { color: #333; font-size: 24px; margin: 0 0 8px; }
        p { color: #666; font-size: 14px; margin: 0 0 24px; }
        label { display: block; font-weight: 600; color: #333; margin-bottom: 6px; font-size: 14px; }
        input[type="text"] { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; box-sizing: border-box; }
        input[type="text"]:focus { outline: none; border-color: #ff6200; }
        button { background: #ff6200; color: #fff; border: none; padding: 12px 24px; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 16px; width: 100%; }
        button:hover { background: #e55500; }
        .info { background: #fff3cd; color: #856404; padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; }
        .info a { color: #ff6200; }
        .toast { position: fixed; bottom: 20px; right: 20px; background: #ff6200; color: white; padding: 16px 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 10000; animation: slideIn 0.3s ease; max-width: 300px; }
        .toast.success { background: #46CF6E; }
        .toast.error { background: #e74c3c; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
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
        <h1>Configurar Mercado Pago</h1>
        <p>Olá, <strong><?php echo htmlspecialchars($username); ?></strong>. Configure seu token de acesso para receber pagamentos via PIX.</p>

        <div class="info">
            <strong>Como obter seu token:</strong>
            <ol style="margin:8px 0 0;padding-left:20px;">
                <li>Crie uma conta em <a href="https://www.mercadopago.com.br" target="_blank">mercadopago.com.br</a></li>
                <li>Vá em <strong>Suas Integrações → Credenciais</strong></li>
                <li>Copie o <strong>Access Token</strong> (production, não o de teste)</li>
                <li>Cole no campo abaixo e salve</li>
            </ol>
        </div>

        <form id="mpForm">
            <label for="mp_token">Access Token do Mercado Pago</label>
            <input type="text" id="mp_token" name="mp_token" value="<?php echo htmlspecialchars($tokenAtual); ?>" placeholder="APP_USR-...">
            <button type="submit">Salvar Token</button>
        </form>
    </div>

    <script>
    document.getElementById('mpForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = this.querySelector('button');
        btn.disabled = true;
        btn.textContent = 'Salvando...';

        const token = document.getElementById('mp_token').value.trim();
        const formData = new FormData();
        formData.append('mp_token', token);

        const response = await fetch('configPagamento.php', { method: 'POST', body: formData });
        const result = await response.json();

        btn.disabled = false;
        btn.textContent = 'Salvar Token';

        showAlert(result.message, result.success ? 'success' : 'error');
    });

    function showAlert(message, type) {
        const existing = document.querySelector('.toast');
        if (existing) existing.remove();
        const toast = document.createElement('div');
        toast.className = 'toast' + (type !== 'info' ? ' ' + type : '');
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
    </script>
</body>
</html>
