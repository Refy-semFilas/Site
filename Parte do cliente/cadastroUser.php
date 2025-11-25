<?php
require "conexao.php";

$user = $_POST["user"];
$email = $_POST["email"];
$senha = password_hash($_POST["senha"], PASSWORD_DEFAULT);

$sql = $conn->prepare("SELECT * FROM usuarios WHERE USERNAME = ? OR EMAIL = ?");
$sql->bind_param("ss", $user, $email);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
    echo "Usuário ou email já cadastrado!";
    exit;
}

$stmt = $conn->prepare("INSERT INTO usuarios (USERNAME, EMAIL, SENHA) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $user, $email, $senha);

if ($stmt->execute()) {
    header("Location: login.html");
} else {
    echo "Erro ao cadastrar usuário.";
}
?>
