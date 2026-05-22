<?php
session_start();
require "../supabaseConnection.php";
require "../userFunctions.php";

if (!isAdmin()) {
    header("Location: ../Partedocliente/loginForm.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["CODIGO_DE_BARRAS"])) {
    $CODIGO_DE_BARRAS = trim($_POST["CODIGO_DE_BARRAS"]);

    if ($CODIGO_DE_BARRAS !== "") {
        $check = supabaseRequest("/rest/v1/produto?codigo_de_barras=eq.$CODIGO_DE_BARRAS&select=usuario_id");
        $produto = $check['data'][0] ?? null;

        if (!$produto || (int)$produto['usuario_id'] !== (int)$_SESSION['user_id']) {
            alerta('Produto não encontrado ou sem permissão!', null, true);
        }

        $delete = supabaseRequest("/rest/v1/produto?codigo_de_barras=eq.$CODIGO_DE_BARRAS", 'DELETE');

        if ($delete['code'] === 200 || $delete['code'] === 204) {
            alerta('Produto excluído com sucesso!', 'dashboard.php');
        } else {
            alerta('Produto não encontrado!', null, true);
        }
    } else {
        alerta('Digite um código de barras!', null, true);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Excluir Produto</title>
    <link rel="stylesheet" href="../css/mainHeader.css">
    <link rel="stylesheet" href="../css/addProduct.css">
</head>
<body>
    <header>
        <div class="imgEpesquisa">
            <div class="imagemLogo">
                <img class="logo" src="../img/Logo png.png" alt="logo">
            </div>
            <div class="pesquisaOpcoes">
                <div class="opcoes">
                    <a href="dashboard.php">Inicio</a>
                    <a href="addProductForm.html">Adicionar item</a>
                    <a href="relatorio.html">Relatório</a>
                </div>
            </div>
        </div>
    </header>

    <div class="conteudo" style="height: 50%;">
        <form method="post" action="deleteProduct.php">
            <h1 class="titleConteudo">Exclua seu&nbsp;<span class="titleConteudoColor">produto</span></h1>
            <div class="coluna codigo" style="margin-top: 15%;">
                <label>Código de barras do produto</label>
                <div class="input-with-icon">
                    <input type="text" name="CODIGO_DE_BARRAS" placeholder="Digite o código de barras">
                </div>
            </div>
            <div class="botoes">
                <a href="dashboard.php" class="btn cancelar" style="margin-top: 10%;">Cancelar</a>
                <button type="submit" class="btn excluir" style="margin-top: 10%;">Excluir</button>
            </div>
        </form>
    </div>
</body>
</html>
