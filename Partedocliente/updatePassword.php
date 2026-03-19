<?php
require "../supabaseConnection.php";

function alerta($mensagem, $redirect = null, $voltar = false)
{
    echo "
    <html>
    <head>
    <style>
        body {
            margin:0;
            font-family: Arial, sans-serif;
            background: transparent;
            display: flex;
            justify-content: center;
            margin-top: 30px
        }

        .alert-box {
            background: linear-gradient(135deg, #ff9100, #ff5e00);
            color: white;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
            font-size: 16px;
            animation: slide 0.4s ease;
            height: 40px;
            width:200px;
            display: flex;
            justify-content: center;
            align-items: center;

        }

        @keyframes slide {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
    </head>

    <body>
        <div class='alert-box'>$mensagem</div>

        <script>
            setTimeout(() => {";

    if ($redirect) {
        echo "window.location.href = '$redirect';";
    }

    if ($voltar) {
        echo "window.history.back();";
    }

    echo "
            }, 2500);
        </script>
    </body>
    </html>
    ";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['token'] ?? '';
    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if (empty($token) || empty($novaSenha) || empty($confirmarSenha)) {
        alerta('Por favor, preencha todos os campos!', null, true);
    }

    if ($novaSenha !== $confirmarSenha) {
        alerta('As senhas não coincidem!', null, true);
    }

    if (strlen($novaSenha) < 6) {
        alerta('A senha deve ter pelo menos 6 caracteres!', null, true);
    }

    $result = supabaseRequest("/rest/v1/password_resets?token=eq.$token&expires_at=gt." . date('Y-m-d H:i:s') . "&select=user_id");

    if (count($result['data']) === 0) {
        alerta('Token inválido ou expirado!', 'forgotPassword.html');
    }

    $userId = $result['data'][0]['user_id'];
    $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

    $updateResult = supabaseRequest("/rest/v1/usuarios?id=eq.$userId", 'PATCH', ['senha' => $senhaHash]);

    if ($updateResult['code'] === 200 || $updateResult['code'] === 204) {
        supabaseRequest("/rest/v1/password_resets?token=eq.$token", 'DELETE');
        alerta('Senha redefinida com sucesso!', 'loginForm.html');
    } else {
        alerta('Erro ao redefinir senha!', null, true);
    }

} else {
    alerta('Método não permitido!', 'loginForm.html');
}
?>
