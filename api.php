<?php
require "databaseConnection.php";

$categoria = $_GET['categoria'] ?? null;

if ($categoria) {
    $sql = $conn->prepare("SELECT id, NOME, DESCRICAO, VALOR, IMAGEM, CATEGORIA, ESTOQUE FROM produto WHERE CATEGORIA = ?");
    $sql->bind_param("s", $categoria);
} else {
    $sql = $conn->prepare("SELECT id, NOME, DESCRICAO, VALOR, IMAGEM, CATEGORIA, ESTOQUE FROM produto");
}

$sql->execute();
$result = $sql->get_result();

$produtos = [];
while ($row = $result->fetch_assoc()) {
    $produtos[] = $row;
}

echo json_encode($produtos);