<?php
require "../databaseConnection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["CODIGO_DE_BARRAS"])) {

    $CODIGO_DE_BARRAS = trim($_POST["CODIGO_DE_BARRAS"]);

    if ($CODIGO_DE_BARRAS !== "") {

        $delete = $conn->prepare(
            "DELETE FROM produto WHERE CODIGO_DE_BARRAS = ?"
        );
        $delete->bind_param("s", $CODIGO_DE_BARRAS);
        $delete->execute();

        if ($delete->affected_rows > 0) {
            echo "<script>
                    alert('Produto excluído com sucesso!');
                    window.location.href = 'dashboard.php';
                  </script>";
            exit;
        } else {
            echo "<script>alert('Produto não encontrado!');</script>";
        }

    } else {
        echo "<script>alert('Digite um código de barras.');</script>";
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
                <div class="barraPesquisa">
                    <svg width="30" height="30" ...></svg>
                    <input type="text" placeholder="Pesquise aqui...">
                </div>
                <div class="opcoes">
                    <a href="dashboard.php">Inicio</a>
                    <a href="addProductForm.html">Adicionar item</a>
                    <a href="relatorio.html">Relatório de venda</a>
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
                    <input type="text" name="CODIGO_DE_BARRAS" placeholder="Digite o código de barras do produto">
                    <svg class="icon" width="22" height="22" ...></svg>
                </div>
            </div>

            <div class="botoes">
                <button type="reset" class="btn cancelar" style="margin-top: 10%;">Cancelar</button>
                <button type="submit" class="btn excluir" style="margin-top: 10%;">Excluir</button>
            </div>
        </form>
    </div>
</body>

</html>