<?php
require "../conexao.php";

// pega os dados do forms
$user = $_POST['user'];
$senha = $_POST['senha'];


$sql = $conn->prepare("SELECT * FROM usuarios WHERE USERNAME = ?");
$sql->bind_param("s", $user);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows === 1) {
    $dados = $result->fetch_assoc();

    // para criptografia
    if (password_verify($senha, $dados['SENHA'])) {
        header("Location: inicio.html");
        exit;

    }

//verifica senha
    else {
        echo "<script>
            alert('Senha incorreta!');
            window.location.href = 'index.html';
        </script>";
        exit;
    }
} 
// se n existir o user
    else {
        echo "<script>
            alert('Usuário não encontrado. Crie um cadastro.');
            window.location.href = 'cadastroUser.html';
        </script>";
        exit;
    }
?>