<?php
session_start();

require "../supabaseConnection.php";
require "../userFunctions.php";

if (!isAdmin()) {
    header("Location: ../Partedocliente/loginForm.html");
    exit;
}

$result = supabaseRequest("/rest/v1/produto?usuario_id=eq." . $_SESSION['user_id'] . "&select=*");
$produtos = $result['data'] ?? [];
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
                    <input type="text" id="searchInput" placeholder="Pesquise aqui...">
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
        <img src="../imgBD/<?php echo $p['imagem']; ?>" alt="<?php echo $p['nome']; ?>">
    </div>

    <div class="info">
        <p class="descricao"><?php echo $p['nome']; ?></p>
        <p class="preco">R$ <?php echo number_format($p['valor'], 2, ',', '.'); ?></p>
    </div>

    <div class="acoes">
        <a class="btn-card alterar" href="editProduct.php?id=<?= $p['id'] ?>">
            Alterar
        </a>


        <form method="post" action="deleteProduct.php" class="delete-form">
            <input type="hidden" name="CODIGO_DE_BARRAS" value="<?php echo $p['codigo_de_barras']; ?>">
            <button type="button" class="btn-card excluir btn-excluir" data-nome="<?php echo htmlspecialchars($p['nome']); ?>">Excluir</button>
        </form>
    </div>
</div>


<?php endforeach; ?>

    </div>

    <div id="modalConfirm" class="modal" style="display:none;">
        <div class="modal-content">
            <h3>Confirmar exclusão</h3>
            <p id="modalTexto">Deseja excluir este produto?</p>
            <div class="modal-botoes">
                <button type="button" class="btn-cancelar" onclick="fecharModal()">Cancelar</button>
                <button type="button" class="btn-confirmar" id="btnConfirmar">Excluir</button>
            </div>
        </div>
    </div>
</body>
</html>

<script>
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.card');
    
    cards.forEach(card => {
        const nome = card.querySelector('.descricao').textContent.toLowerCase();
        card.style.display = nome.includes(searchTerm) ? 'flex' : 'none';
    });
});

document.querySelectorAll('.btn-excluir').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var nome = this.getAttribute('data-nome');
        var form = this.closest('.delete-form');
        document.getElementById('modalTexto').textContent = 'Deseja excluir "' + nome + '"?';
        document.getElementById('modalConfirm').style.display = 'flex';
        document.getElementById('btnConfirmar').onclick = function() {
            form.submit();
        };
    });
});

function fecharModal() {
    document.getElementById('modalConfirm').style.display = 'none';
}
</script>

<style>
.modal {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
.modal-content {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    text-align: center;
    max-width: 400px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}
.modal-content h3 {
    margin: 0 0 15px 0;
    color: #333;
}
.modal-content p {
    color: #666;
    margin-bottom: 25px;
}
.modal-botoes {
    display: flex;
    gap: 15px;
    justify-content: center;
}
.btn-cancelar {
    padding: 10px 25px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    background: #ccc;
    color: #333;
    font-size: 14px;
}
.btn-confirmar {
    padding: 10px 25px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    background: #e74c3c;
    color: #fff;
    font-size: 14px;
}
.btn-cancelar:hover { background: #bbb; }
.btn-confirmar:hover { background: #c0392b; }
</style>
