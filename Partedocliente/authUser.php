<?php
require "../supabaseConnection.php";

$user = $_POST['user'];
$senha = $_POST['senha'];

$result = supabaseRequest("/rest/v1/usuarios?username=eq.$user&select=*");

if (count($result['data']) === 1) {
    $dados = $result['data'][0];

    if (password_verify($senha, $dados['senha'])) {
        session_start();
        $_SESSION['user_id'] = $dados['id'];
        $_SESSION['username'] = $dados['username'];
        $_SESSION['tipo'] = $dados['tipo'] ?? 'cliente';

        if ($_SESSION['tipo'] === 'admin') {
            header("Location: ../Partedocantineiro/dashboard.php");
        } else {
            header("Location: home.html");
        }
        exit;
    } else {
        alerta('Senha incorreta', null, true);
        exit;
    }
} else {
    alerta('Usuario não encontrado. Cadastre-se!', 'registerUserForm.html');
    exit;
}
?>
