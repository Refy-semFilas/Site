<?php
include 'conexao.php';

if(isset($_POST['adicionar'])){
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];
    $codigoDeBarras = $_POST['codigoDeBarras'];

    $sql = "INSERT INTO usuarios (nome, descricao, valor, codigoDeBarras) VALUES ('$nome', '$descricao', '$valor', '$codigoDeBarras')";
    
    if(mysqli_query($conexao, $sql)){
        echo "Usuário cadastrado com sucesso!";
    } else {
        echo "Erro: " . mysqli_error($conexao);
    }
}
?>

<form method="POST">
    Nome: <input type="text" name="nome"><br>
    Descrição: <input type="text" name="descricao"><br>
    Valor: <input type="text" name="valor"><br>
    Código de barras: <input type="text" name="codigoDeBarras"><br>
    <input type="submit" name="adicionar" value="Adicionar">
</form>