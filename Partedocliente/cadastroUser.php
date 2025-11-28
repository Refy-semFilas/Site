<?php
require "conexao.php";

$username = $_POST["user"];
$email = $_POST["email"];
$senha = password_hash($_POST["senha"], PASSWORD_DEFAULT);

$sql = $conn->prepare("SELECT * FROM usuarios WHERE USERNAME = ? OR EMAIL = ?");
$sql->bind_param("ss", $username, $email);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
    echo "<script>
        alert('Usuário ou email já cadastrado!');
        window.location.href = 'index.html';
        </script>";
    exit;
}

$stmt = $conn->prepare("INSERT INTO usuarios (USERNAME, EMAIL, SENHA) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $senha);

if ($stmt->execute()) {
    header("Location: index.html");
} else {
    echo "Erro ao cadastrar usuário.";
}
?>
