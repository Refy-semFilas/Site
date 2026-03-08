<?php
session_start();

require "../databaseConnection.php";
require "../userFunctions.php";

if (!isAdmin()) {
    header("Location: ../Partedocliente/loginForm.html");
    exit;
}

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
    <link rel="stylesheet" href="../css/mainHeader.css">
    <link rel="stylesheet" href="../css/adminProductCard.css">
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
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2.2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="6"></circle>
                        <line x1="15.7" y1="15.7" x2="20.2" y2="20.2"></line>
                    </svg>
                    <input type="text" placeholder="Pesquise aqui...">
                </div>
                <div class="opcoes">
                    <a href="dashboard.php" style="border-bottom: 1px solid #073c05;">Inicio</a>
                    <a href="addProductForm.html">Adicionar item</a>
                    <a href="relatorio.html">Relatório de venda</a>
                    <a href="conta.php" aria-label="Minha conta">
                        <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" style="cursor:pointer;">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </a>
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
        <a class="btn-card alterar" href="editProduct.php?id=<?= $p['ID'] ?>">
            Alterar
        </a>


        <form method="post" action="deleteProduct.php"
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
