<?php
require "../supabaseConnection.php";
require "../config.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    
    // Validar campos
    if (empty($email) || empty($username)) {
        echo json_encode([
            'success' => false,
            'message' => 'Por favor, preencha todos os campos!'
        ]);
        exit;
    }
    
    // Validar formato do email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Por favor, digite um email válido!'
        ]);
        exit;
    }
    
    // Verificar se o usuário existe com esse email e username
    $result = supabaseRequest("/rest/v1/usuarios?email=eq.$email&username=eq.$username&select=id,username,email");
    
    if (count($result['data']) === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Usuário ou email não encontrados! Verifique os dados e tente novamente.'
        ]);
        exit;
    }
    
    $user = $result['data'][0];
    
    // Gerar token único para recuperação
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+' . TOKEN_EXPIRY_HOURS . ' hour'));
    
    // Gerar link de redefinição
    $resetLink = generateResetLink($token);
    
    // Criar corpo do email HTML
    $emailBody = createRecoveryEmailBody($username, $resetLink);
    
    // Enviar email
    $emailSubject = "🔐 Recuperação de Senha - Cantina System";
    $emailSent = sendEmail($email, $emailSubject, $emailBody, true);
    
    // Registrar log para debug
    logEmail($email, $emailSubject, $emailSent);
    
    // Para este exemplo, vamos salvar o token em uma tabela temporária
    // Se você não tiver tabela, pode salvar em arquivo ou sessão
    try {
        // Limpar tokens antigos deste usuário
        $deleteOld = supabaseRequest("/rest/v1/password_resets?user_id=eq." . $user['id'], 'DELETE');
        
        // Inserir novo token
        $insertToken = supabaseRequest("/rest/v1/password_resets", 'POST', [
            'user_id' => $user['ID'],
            'token' => $token,
            'expires_at' => $expires
        ]);
        
        if ($emailSent) {
            echo json_encode([
                'success' => true,
                'message' => '📧 Email enviado com sucesso!\\n\\nVerifique sua caixa de entrada (e a pasta de spam) e clique no link para redefinir sua senha.\\n\\n⚠️ O link expirará em 1 hora.',
                'redirect' => true,
                'redirect_url' => 'resetPasswordForm.html?token=' . $token,
                'debug_token' => $token // Remover em produção!
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => '❌ Erro ao enviar email. Tente novamente mais tarde.\\n\\nSe o problema persistir, entre em contato com o suporte.'
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao processar sua solicitação. Tente novamente mais tarde.'
        ]);
    }
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido!'
    ]);
}
?>