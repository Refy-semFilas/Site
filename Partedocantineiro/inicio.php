<?php
require "../PartedoCliente/conexao.php";

// Buscar todos os produtos
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
                    <a href="excluiItem.html">Excluir item</a>
                    <a href="alteraItem.html">alterar item</a>
                    <a href="relatorio.html">Relatório de venda</a>
                </div>
            </div>
        </div>
    </header>

    <div class="conteudo">

        <!-- 🔥 LISTA DINÂMICA DE PRODUTOS -->
        <?php foreach ($produtos as $p): ?>

            <div class="card">
                <div class="imagem">
                    <img 
                        src="../imgBD/<?php echo htmlspecialchars($p['IMAGEM']); ?>" 
                        alt="<?php echo htmlspecialchars($p['NOME']); ?>"
                        onerror="this.src='../img/placeholder.png'"
                    >
                </div>

                <div class="info">
                    <div class="descricao">
                        <p><?php echo htmlspecialchars($p['NOME']); ?></p>
                    </div>

                    <div class="preco">
                        <p>R$ <?php echo number_format($p['VALOR'], 2, ',', '.'); ?></p>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>

    </div>
</body>
</html>
