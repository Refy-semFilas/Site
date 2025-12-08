<?php
require "../PartedoCliente/conexao.php";

$nome = $_POST["nome"];
$descricao = $_POST["descricao"];
$valor = $_POST["valor"];
$codigo = $_POST["codigoBarras"];

$imagemNome = null;

if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] === UPLOAD_ERR_OK) {
    $imagemNome = uniqid() . "-" . $_FILES["foto"]["name"]; 
    move_uploaded_file($_FILES["foto"]["tmp_name"], "../imgBD/" . $imagemNome);
}

$sql = $conn->prepare("
    INSERT INTO produto (NOME, DESCRICAO, VALOR, CODIGO_DE_BARRAS, IMAGEM)
    VALUES (?, ?, ?, ?, ?)
");

$sql->bind_param("ssdss", $nome, $descricao, $valor, $codigo, $imagemNome);

if ($sql->execute()) {
    echo "<script>
        alert('Produto cadastrado com sucesso!');
        window.location.href = 'addItem.html';
    </script>";
} else {
    echo "Erro ao cadastrar produto: " . $conn->error;
}
?>
