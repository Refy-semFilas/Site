<?php
include 'conexao.php';

if(isset($_POST['atualizar'])){
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $CPF = $_POST['CPF'];
    $telefone = $_POST['telefone'];

    $sql = "UPDATE cliente SET nome='$nome', CPF='$CPF', telefone='$telefone' WHERE id='$id'";

    if(mysqli_query($conexao, $sql)){
        echo "Cliente atualizado!";
    } else {
        echo "Erro: " . mysqli_error($conexao);
    }
}
?>

<form method="POST">
    ID do cliente: <input type="number" name="id"><br>
    Nome: <input type="text" name="nome"><br>
    CPF: <input type="text" name="CPF"><br>
    Telefone: <input type="text" name="telefone"><br>
    <input type="submit" name="atualizar" value="Atualizar">
</form>
