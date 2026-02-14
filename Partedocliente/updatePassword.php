<?php
require "../databaseConnection.php";

function alerta($mensagem, $redirect = null, $voltar = false) {
    echo "
    <html>
    <head>
    <style>
        body {
            margin:0;
            font-family: Arial, sans-serif;
            background: transparent;
        }

        .alert-box {
            position: fixed;
            top: 30px;
            right: 30px;
            background: linear-gradient(135deg, #ff0000, #990000);
            color: white;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
            font-size: 16px;
            animation: slide 0.4s ease;
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

    // Validar campos
    if (empty($token) || empty($novaSenha) || empty($confirmarSenha)) {
        alerta('Por favor, preencha todos os campos!', null, true);
    }

    // Validar senhas
    if ($novaSenha !== $confirmarSenha) {
        alerta('As senhas não coincidem!', null, true);
    }

    if (strlen($novaSenha) < 6) {
        alerta('A senha deve ter pelo menos 6 caracteres!', null, true);
    }

    // Verificar token
    $sql = $conn->prepare("
        SELECT pr.user_id, pr.expires_at 
        FROM password_resets pr 
        WHERE pr.token = ? AND pr.expires_at > NOW()
    ");
    $sql->bind_param("s", $token);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows === 0) {
        alerta(
            'Token inválido ou expirado! Solicite novamente a recuperação.',
            'forgotPassword.html'
        );
    }

    $resetData = $result->fetch_assoc();
    $userId = $resetData['user_id'];

    // Atualizar senha
    $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
    $updateStmt = $conn->prepare("UPDATE usuarios SET SENHA = ? WHERE ID = ?");
    $updateStmt->bind_param("si", $senhaHash, $userId);

    if ($updateStmt->execute()) {

        // Deletar token usado
        $deleteStmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
        $deleteStmt->bind_param("s", $token);
        $deleteStmt->execute();

        alerta(
            'Senha redefinida com sucesso! Faça login com sua nova senha.',
            'loginForm.html'
        );

    } else {

        alerta(
            'Erro ao redefinir senha. Tente novamente ou contate o suporte.',
            null,
            true
        );
    }

} else {

    alerta(
        'Método não permitido!',
        'loginForm.html'
    );
}
?>