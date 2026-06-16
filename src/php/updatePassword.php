<?php
require "supabaseConnection.php";

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
        alerta('Token inválido ou expirado!', '../../forgotPassword.html');
    }

    $userId = $result['data'][0]['user_id'];
    $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

    $updateResult = supabaseRequest("/rest/v1/usuarios?id=eq.$userId", 'PATCH', ['senha' => $senhaHash]);

    if ($updateResult['code'] === 200 || $updateResult['code'] === 204) {
        supabaseRequest("/rest/v1/password_resets?token=eq.$token", 'DELETE');
        alerta('Senha redefinida com sucesso!', '../../loginForm.html');
    } else {
        alerta('Erro ao redefinir senha!', null, true);
    }

} else {
    alerta('Método não permitido!', '../../loginForm.html');
}
?>
