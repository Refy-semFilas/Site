<?php
header('Content-Type: application/json');
require "supabaseConnection.php";

function jsonError($message) {
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

$input = $_POST;

if (empty($input["user"]) || empty($input["email"]) || empty($input["senha"])) {
    jsonError('Por favor, preencha todos os campos!');
}

$username = trim($input["user"]);
$email = trim($input["email"]);
$senha = $input["senha"];
$tipo = $input["tipo"] ?? 'cliente';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonError('Por favor, digite um email válido!');
}

if (strlen($senha) < 6) {
    jsonError('A senha deve ter pelo menos 6 caracteres!');
}

$result = supabaseRequest("/rest/v1/usuarios?email=eq." . urlencode($email) . "&select=id");

if (count($result['data']) > 0) {
    jsonError('Este email já está em uso!');
}

$result = supabaseRequest("/rest/v1/usuarios?username=eq." . urlencode($username) . "&select=id");

if (count($result['data']) > 0) {
    jsonError('Nome de usuário já está em uso!');
}

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$data = [
    'username' => $username,
    'email' => $email,
    'senha' => $senhaHash,
    'tipo' => $tipo,
];

$insertResult = supabaseRequest("/rest/v1/usuarios", 'POST', $data);

if ($insertResult['code'] === 201) {
    echo json_encode([
        'success' => true,
        'message' => 'Cadastro realizado com sucesso!',
        'redirect' => 'loginForm.html'
    ]);
} else {
    jsonError('Erro ao cadastrar usuário!');
}
?>
