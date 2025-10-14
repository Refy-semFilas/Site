<?php
include 'conexao.php';

if(isset($_POST['deletar'])){
    $id = $_POST['id'];
    $sql = "DELETE FROM cliente WHERE id=$id";

    if(mysqli_query($conexao, $sql)){
        echo "Cliente deletado!";
    } else {
        echo "Erro: " . mysqli_error($conexao);
    }
}
?>

<form method="POST">
    ID do cliente: <input type="number" name="id"><br>
    <input type="submit" name="deletar" value="Deletar">
</form>
