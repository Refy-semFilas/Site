<?php
include 'conexao.php';

if(isset($_POST['deletar'])){
    $codigoDeBarra = $_POST['codigoDeBarra'];
    $sql = "DELETE FROM produto WHERE codigoDeBarra=$codigoDeBarra";

    if(mysqli_query($conexao, $sql)){
        echo "Produto deletado!";
    } else {
        echo "Erro: " . mysqli_error($conexao);
    }
}
?>

<form method="POST">
    Código de barras do produto: <input type="text" name="codigoDeBarra"><br>
    <input type="submit" name="deletar" value="Deletar">
</form>
