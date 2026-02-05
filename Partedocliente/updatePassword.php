<?php
require "../databaseConnection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';
    
    // Validar campos
    if (empty($token) || empty($novaSenha) || empty($confirmarSenha)) {
        echo "<script>
            alert('Por favor, preencha todos os campos!');
            window.history.back();
        </script>";
        exit;
    }
    
    // Validar senhas
    if ($novaSenha !== $confirmarSenha) {
        echo "<script>
            alert('As senhas não coincidem!');
            window.history.back();
        </script>";
        exit;
    }
    
    if (strlen($novaSenha) < 6) {
        echo "<script>
            alert('A senha deve ter pelo menos 6 caracteres!');
            window.history.back();
        </script>";
        exit;
    }
    
    // Verificar se o token é válido e não expirou
    $sql = $conn->prepare("
        SELECT pr.user_id, pr.expires_at 
        FROM password_resets pr 
        WHERE pr.token = ? AND pr.expires_at > NOW()
    ");
    $sql->bind_param("s", $token);
    $sql->execute();
    $result = $sql->get_result();
    
    if ($result->num_rows === 0) {
        echo "<script>
            alert('⚠️ Token inválido ou expirado!\\n\\nSolicite novamente a recuperação de senha.');
            window.location.href = 'forgotPassword.html';
        </script>";
        exit;
    }
    
    $resetData = $result->fetch_assoc();
    $userId = $resetData['user_id'];
    
    // Atualizar a senha do usuário
    $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
    $updateStmt = $conn->prepare("UPDATE usuarios SET SENHA = ? WHERE ID = ?");
    $updateStmt->bind_param("si", $senhaHash, $userId);
    
    if ($updateStmt->execute()) {
        // Limpar o token usado
        $deleteStmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
        $deleteStmt->bind_param("s", $token);
        $deleteStmt->execute();
        
        echo "<script>
            alert('✅ Senha redefinida com sucesso!\\n\\nFaça login com sua nova senha.');
            window.location.href = 'loginForm.html';
        </script>";
    } else {
        echo "<script>
            alert('❌ Erro ao redefinir senha. Tente novamente.\\n\\nSe o problema persistir, entre em contato com o suporte.');
            window.history.back();
        </script>";
    }
    
} else {
    echo "<script>
        alert('Método não permitido!');
        window.location.href = 'loginForm.html';
    </script>";
}
?>