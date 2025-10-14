<?php
include 'conexao.php';

if(isset($_POST['atualizar'])){
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];
    $codigoDeBarras = $_POST['codigoDeBarras'];

    $sql = "UPDATE produto SET nome='$nome', descricao='$descricao', valor='$valor', codigoDeBarras='$codigoDeBarras' WHERE id='$id'";

    if(mysqli_query($conexao, $sql)){
        echo "Produto atualizado!";
    } else {
        echo "Erro: " . mysqli_error($conexao);
    }
}
?>

<form method="POST">
    ID do produto: <input type="number" name="id"><br>
    Nome: <input type="text" name="nome"><br>
    Descrição: <input type="text" name="descricao"><br>
    Valor: <input type="text" name="valor"><br>
    Código de barras: <input type="text" name="codigoDeBarras"><br>
    <input type="submit" name="atualizar" value="Atualizar">
</form>
