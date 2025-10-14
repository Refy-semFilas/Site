<?php
include 'conexao.php';

if(isset($_POST['cadastrar'])){
    $nome = $_POST['nome'];
    $CPF = $_POST['CPF'];
    $telefone = $_POST['telefone'];

    $sql = "INSERT INTO cliente (nome, CPF, telefone) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conexao, $sql);
    
    if(mysqli_query($conexao, $sql)){
        echo "Usuário cadastrado com sucesso!";
    } else {
        echo "Erro: " . mysqli_error($conexao);
    }
}
?>

<form method="POST">
    Nome: <input type="text" name="nome"><br>
    CPF: <input type="text" name="CPF"><br>
    Telefone: <input type="text" name="telefone"><br>
    <input type="submit" name="cadastrar" value="Cadastrar">
</form>