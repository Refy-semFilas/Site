<?php
session_start();
require "../supabaseConnection.php";
require "../userFunctions.php";

if (!isAdmin()) {
    header("Location: ../../../loginForm.html");
    exit;
}

$nome = $_POST["nome"];
$descricao = $_POST["descricao"];
$valor = str_replace(',', '.', str_replace('.', '', $_POST["valor"]));
$estoque = $_POST["estoque"];
$codigo = $_POST["codigoBarras"];
$categoria = $_POST['categoria'];

$imagemNome = null;

if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] === UPLOAD_ERR_OK) {
    $imagemNome = uniqid() . "-" . $_FILES["foto"]["name"];
    $ext = strtolower(pathinfo($imagemNome, PATHINFO_EXTENSION));
    $contentType = $ext === 'png' ? 'image/png' : ($ext === 'gif' ? 'image/gif' : 'image/jpeg');
    $upload = supabaseStorageUpload('produtos', $_FILES["foto"]["tmp_name"], $imagemNome, $contentType);
    if (isset($upload['error'])) {
        $imagemNome = null;
    }
}

$check = supabaseRequest("/rest/v1/produto?codigo_de_barras=eq.$codigo&select=id");

if (count($check['data']) > 0) {
    alerta('Código de barras já cadastrado!', '../../../addProductForm.html');
}

$insert = supabaseRequest("/rest/v1/produto", 'POST', [
    'nome' => $nome,
    'descricao' => $descricao,
    'valor' => floatval($valor),
    'codigo_de_barras' => $codigo,
    'imagem' => $imagemNome,
    'categoria' => $categoria,
    'estoque' => intval($estoque),
    'usuario_id' => $_SESSION['user_id']
]);

if ($insert['code'] === 201) {
    alerta('Produto cadastrado com sucesso!', 'dashboard.php');
} else {
    alerta('Erro ao cadastrar produto!', '../../../addProductForm.html');
}
?>
