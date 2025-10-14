<?php
include 'conexao.php';

$sql = "SELECT * FROM cliente";
$resultado = mysqli_query($conexao, $sql);

echo "<table border=1 width=300><tr><th>ID</th><th>NOME</th><th>CPF</th><th>TELEFONE</th></tr>";
while($cliente = mysqli_fetch_assoc($resultado)){
    echo "<tr><td>".$linha[0]."</td><td>".$linha[1]."</td><td>".$linha[2]."</td><td>".$linha[3]."</td></tr>";
}
?>