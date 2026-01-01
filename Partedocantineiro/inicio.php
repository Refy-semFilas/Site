<?php
require "../conexao.php";

// Busca todos os produtos
$sql = $conn->query("SELECT * FROM produto");
$produtos = $sql->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="icon" href="../img/Logo png.png">
    <link rel="stylesheet" href="../Partedocliente/header.css">
    <link rel="stylesheet" href="cardCantina.css">
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
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2.2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="6"></circle>
                        <line x1="15.7" y1="15.7" x2="20.2" y2="20.2"></line>
                    </svg>
                    <input type="text" placeholder="Pesquise aqui...">
                </div>
                <div class="opcoes">
                    <a href="inicio.php" style="border-bottom: 1px solid #073c05;">Inicio</a>
                    <a href="addItem.html">Adicionar item</a>
                    <a href="relatorio.html">Relatório de venda</a>
                </div>
            </div>
        </div>
    </header>

    <div class="conteudo">

        <?php foreach ($produtos as $p): ?>

    <div class="card">
    <div class="imagem">
        <img src="../imgBD/<?php echo $p['IMAGEM']; ?>" alt="<?php echo $p['NOME']; ?>">
    </div>

    <div class="info">
        <p class="descricao"><?php echo $p['NOME']; ?></p>
        <p class="preco">R$ <?php echo number_format($p['VALOR'], 2, ',', '.'); ?></p>
    </div>

    <div class="acoes">
        <a class="btn-card alterar" href="alterarProduto.php?id=<?= $p['ID'] ?>">
            Alterar
        </a>


        <form method="post" action="excluirProduto.php"
              onsubmit="return confirm('Deseja excluir este produto?');">
            <input type="hidden" name="id" value="<?php echo $p['ID']; ?>">
            <button type="submit" class="btn-card excluir">Excluir</button>
        </form>
    </div>
</div>


<?php endforeach; ?>


    </div>
</body>
</html>
