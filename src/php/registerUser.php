<?php
require "supabaseConnection.php";

if (empty($_POST["user"]) || empty($_POST["email"]) || empty($_POST["senha"])) {
    alerta('Por favor, preencha todos os campos!', '../registerUserForm.html');
}

$username = trim($_POST["user"]);
$email = trim($_POST["email"]);
$senha = $_POST["senha"];
$tipo = $_POST["tipo"] ?? 'cliente';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    alerta('Por favor, digite um email válido!', '../registerUserForm.html');
}

if (strlen($senha) < 6) {
    alerta('A senha deve ter pelo menos 6 caracteres!', '../registerUserForm.html');
}

$result = supabaseRequest("/rest/v1/usuarios?email=eq." . urlencode($email) . "&select=id");

if (count($result['data']) > 0) {
    alerta('Este email já está em uso!', '../registerUserForm.html');
}

$result = supabaseRequest("/rest/v1/usuarios?username=eq." . urlencode($username) . "&select=id");

if (count($result['data']) > 0) {
    alerta('Nome de usuário já está em uso!', '../registerUserForm.html');
}

$chave_pix = $tipo === 'admin' ? trim($_POST["chave_pix"] ?? '') : null;

if ($tipo === 'admin' && empty($chave_pix)) {
    alerta('Vendedor deve informar uma chave PIX!', '../registerUserForm.html');
}

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$insertResult = supabaseRequest("/rest/v1/usuarios", 'POST', [
    'username' => $username,
    'email' => $email,
    'senha' => $senhaHash,
    'tipo' => $tipo,
    'chave_pix' => $chave_pix
]);

if ($insertResult['code'] === 201) {
    alerta('Cadastro realizado com sucesso!', '../loginForm.html');
} else {
    alerta('Erro ao cadastrar usuário!', '../registerUserForm.html');
}
?>
