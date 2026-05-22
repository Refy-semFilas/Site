<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../../../loginForm.html");
    exit;
}

require "../supabaseConnection.php";

$user_id = $_SESSION['user_id'];
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = trim($_POST['mp_token'] ?? '');
    $update = supabaseRequest("/rest/v1/usuarios?id=eq.$user_id", 'PATCH', [
        'mp_access_token' => $token
    ]);

    if ($update['code'] === 200 || $update['code'] === 201 || $update['code'] === 204) {
        $mensagem = 'Token salvo com sucesso!';
    } else {
        $mensagem = 'Erro ao salvar token.';
    }
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
        body { font-family: 'Open Sans', sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h1 { color: #333; font-size: 24px; margin: 0 0 8px; }
        p { color: #666; font-size: 14px; margin: 0 0 24px; }
        label { display: block; font-weight: 600; color: #333; margin-bottom: 6px; font-size: 14px; }
        input[type="text"] { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; box-sizing: border-box; }
        input[type="text"]:focus { outline: none; border-color: #ff6200; }
        button { background: #ff6200; color: #fff; border: none; padding: 12px 24px; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 16px; width: 100%; }
        button:hover { background: #e55500; }
        .msg { padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
        .msg.sucesso { background: #d4edda; color: #155724; }
        .msg.erro { background: #f8d7da; color: #721c24; }
        .info { background: #fff3cd; color: #856404; padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; }
        .info a { color: #ff6200; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 4px; font-size: 12px; word-break: break-all; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Configurar Mercado Pago</h1>
        <p>Olá, <strong><?php echo htmlspecialchars($username); ?></strong>. Configure seu token de acesso para receber pagamentos via PIX.</p>

        <?php if ($mensagem): ?>
            <div class="msg <?php echo strpos($mensagem, 'sucesso') !== false ? 'sucesso' : 'erro'; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <div class="info">
            <strong>Como obter seu token:</strong>
            <ol style="margin:8px 0 0;padding-left:20px;">
                <li>Crie uma conta em <a href="https://www.mercadopago.com.br" target="_blank">mercadopago.com.br</a></li>
                <li>Vá em <strong>Suas Integrações → Credenciais</strong></li>
                <li>Copie o <strong>Access Token</strong> (production, não o de teste)</li>
                <li>Cole no campo abaixo e salve</li>
            </ol>
        </div>

        <form method="post">
            <label for="mp_token">Access Token do Mercado Pago</label>
            <input type="text" id="mp_token" name="mp_token" value="<?php echo htmlspecialchars($tokenAtual); ?>" placeholder="APP_USR-...">
            <button type="submit">Salvar Token</button>
        </form>
    </div>
</body>
</html>
