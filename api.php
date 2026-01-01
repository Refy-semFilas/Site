<?php
include "conexao.php";

$sql = "SELECT * FROM produto";
$result = mysqli_query($conn, $sql);

$produtos = [];

while ($row = mysqli_fetch_assoc($result)) {
    $produtos[] = $row;
}

echo json_encode($produtos);
