<?php
require "conexao.php";

$user = $_POST["user"];
$senha = $_POST["senha"];

$sql = $conn->prepare("SELECT * FROM usuarios WHERE USERNAME = ?");
$sql->bind_param("s", $user);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows === 0) {
    echo "Usuário não encontrado!";
    exit;
}

$usuario = $result->fetch_assoc();

// Verificar senha
if (password_verify($senha, $usuario["SENHA"])) {
    session_start();
    $_SESSION["user"] = $usuario["USERNAME"];
    header("Location: inicio.html");
} else {
    echo "Senha incorreta!";
}
?>
