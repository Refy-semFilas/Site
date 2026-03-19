<?php
require "../supabaseConnection.php";

$nome = $_POST["nome"];
$descricao = $_POST["descricao"];
$valor = $_POST["valor"];
$estoque = $_POST["estoque"];
$codigo = $_POST["codigoBarras"];
$categoria = $_POST['categoria'];

$imagemNome = null;

if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] === UPLOAD_ERR_OK) {
    $imagemNome = uniqid() . "-" . $_FILES["foto"]["name"]; 
    move_uploaded_file($_FILES["foto"]["tmp_name"], "../imgBD/" . $imagemNome);
}

$check = supabaseRequest("/rest/v1/produto?codigo_de_barras=eq.$codigo&select=id");

if (count($check['data']) > 0) {
    alerta('Código de barras já cadastrado!', 'addProductForm.html');
}

$insert = supabaseRequest("/rest/v1/produto", 'POST', [
    'nome' => $nome,
    'descricao' => $descricao,
    'valor' => floatval($valor),
    'codigo_de_barras' => $codigo,
    'imagem' => $imagemNome,
    'categoria' => $categoria,
    'estoque' => intval($estoque)
]);

if ($insert['code'] === 201) {
    alerta('Produto cadastrado com sucesso!', 'dashboard.php');
} else {
    alerta('Erro ao cadastrar produto!', 'addProductForm.html');
}
?>
