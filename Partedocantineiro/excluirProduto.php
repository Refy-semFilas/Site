<?php
require "../PartedoCliente/conexao.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $codigo = $_POST["codigoBarras"];

    if (!empty($codigo)) {
        $consulta = $conn->prepare("SELECT * FROM produto WHERE CODIGO_DE_BARRAS = ?");
        $consulta->bind_param("s", $codigo);
        $consulta->execute();
        $resultado = $consulta->get_result();

        if ($resultado->num_rows > 0) {
            $delete = $conn->prepare("DELETE FROM produto WHERE CODIGO_DE_BARRAS = ?");
            $delete->bind_param("s", $codigo);

            if ($delete->execute()) {
                echo "<script>
                        alert('Produto excluído com sucesso!');
                        window.location.href = 'excluirProduto.php';
                      </script>";
                exit;
            } else {
                echo "<script>alert('Erro ao excluir.');</script>";
            }
        } else {
            echo "<script>alert('Produto não encontrado!');</script>";
        }
    } else {
        echo "<script>alert('Digite um código.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Excluir Produto</title>
    <link rel="stylesheet" href="../Partedocliente/header.css">
    <link rel="stylesheet" href="newProduto.css">
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
                    <a href="inicio.php">Inicio</a>
                    <a href="addItem.html">Adicionar item</a>
                    <a href="excluirProduto.php" style="border-bottom: 1px solid #073c05;">Excluir item</a>
                    <a href="alteraItem.html">alterar item</a>
                    <a href="relatorio.html">Relatório de venda</a>
                </div>
            </div>
        </div>
    </header>

    <div class="conteudo" style="height: 50%;">
        <form method="post" action="excluirProduto.php">
            <h1 class="titleConteudo">Exclua seu&nbsp;<span class="titleConteudoColor">produto</span></h1>

            <div class="coluna codigo" style="margin-top: 15%;">
                <label>Código de barras do produto</label>
                <div class="input-with-icon">
                    <input type="text" name="codigoBarras" placeholder="Digite o código de barras do produto">
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