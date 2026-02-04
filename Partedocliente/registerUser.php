<?php
require "../databaseConnection.php";

$username = $_POST["user"];
$email = $_POST["email"];
$senha = password_hash($_POST["senha"], PASSWORD_DEFAULT);
echo "<pre>";
print_r($_POST);
echo "</pre>";

$tipo = $_POST["tipo"] ?? 'cliente';

$sql = $conn->prepare("SELECT * FROM usuarios WHERE USERNAME = ? OR EMAIL = ?");
$sql->bind_param("ss", $username, $email);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
    echo "<script>
        window.location.href = 'loginForm.html';
        </script>";
    exit;
}

$stmt = $conn->prepare("INSERT INTO usuarios (USERNAME, EMAIL, SENHA, TIPO) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $senha, $tipo);

if ($stmt->execute()) {
    header("Location: loginForm.html");
} else {
    echo "Erro ao cadastrar usuário.";
}
?>
