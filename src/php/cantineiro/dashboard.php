<?php
session_start();

require "../supabaseConnection.php";
require "../userFunctions.php";

if (!isAdmin()) {
    header("Location: ../../../loginForm.html");
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
    <link rel="icon" href="../../../img/Logo png.png">
    <link rel="stylesheet" href="../../../src/styles/mainHeader.css">
    <link rel="stylesheet" href="../../../src/styles/adminProductCard.css">
</head>

<body>
    <header>
        <div class="imgEpesquisa">
            <div class="imagemLogo">
                <img class="logo" src="../../../img/Logo png.png" alt="logo">
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
                    <a href="../../../addProductForm.html">Adicionar item</a>
                    <a href="configPagamento.php">Pagamento</a>
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

    <div class="card" onclick="abrirDetalhe(this)"
         data-id="<?= $p['id'] ?>"
         data-nome="<?= htmlspecialchars($p['nome']) ?>"
         data-preco="R$ <?= number_format($p['valor'], 2, ',', '.') ?>"
         data-descricao="<?= htmlspecialchars($p['descricao'] ?? '') ?>"
         data-estoque="<?= $p['estoque'] ?>"
         data-imagem="<?= SUPABASE_STORAGE_URL ?>produtos/<?= $p['imagem'] ?>"
         data-codigo="<?= htmlspecialchars($p['codigo_de_barras']) ?>">
    <div class="imagem">
        <img src="<?= SUPABASE_STORAGE_URL ?>produtos/<?php echo $p['imagem']; ?>" alt="<?php echo $p['nome']; ?>">
    </div>

    <div class="info">
        <p class="descricao"><?php echo $p['nome']; ?></p>
        <p class="preco">R$ <?php echo number_format($p['valor'], 2, ',', '.'); ?></p>
    </div>

    <div class="acoes">
        <a class="btn-card alterar" href="editProduct.php?id=<?= $p['id'] ?>" onclick="event.stopPropagation()">
            Alterar
        </a>


        <form method="post" action="deleteProduct.php" class="delete-form">
            <input type="hidden" name="CODIGO_DE_BARRAS" value="<?php echo $p['codigo_de_barras']; ?>">
            <button type="button" class="btn-card excluir btn-excluir" data-nome="<?php echo htmlspecialchars($p['nome']); ?>" onclick="event.stopPropagation()">Excluir</button>
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

    <div id="modalDetalhe" class="modal" style="display:none;" onclick="fecharDetalhe()">
        <div class="modal-detalhe" onclick="event.stopPropagation()">
            <button class="btn-fechar" onclick="fecharDetalhe()">&times;</button>
            <div class="detalhe-imagem">
                <img id="detImagem" src="">
            </div>
            <div class="detalhe-info">
                <h2 id="detNome"></h2>
                <p class="detalhe-preco" id="detPreco"></p>
                <p class="detalhe-descricao" id="detDescricao"></p>
                <p class="detalhe-estoque" id="detEstoque"></p>
                <div class="detalhe-acoes">
                    <a class="btn-card alterar" id="detAlterar" href="">Alterar</a>
                    <button type="button" class="btn-card excluir" id="detExcluir">Excluir</button>
                </div>
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

function abrirDetalhe(card) {
    document.getElementById('detImagem').src = card.dataset.imagem;
    document.getElementById('detNome').textContent = card.dataset.nome;
    document.getElementById('detPreco').textContent = card.dataset.preco;
    document.getElementById('detDescricao').textContent = card.dataset.descricao || 'Sem descrição';
    document.getElementById('detEstoque').textContent = 'Estoque: ' + card.dataset.estoque + ' unidades';
    document.getElementById('detAlterar').href = 'editProduct.php?id=' + card.dataset.id;

    document.getElementById('detExcluir').onclick = function() {
        fecharDetalhe();
        var nome = card.dataset.nome;
        var form = card.querySelector('.delete-form');
        document.getElementById('modalTexto').textContent = 'Deseja excluir "' + nome + '"?';
        document.getElementById('modalConfirm').style.display = 'flex';
        document.getElementById('btnConfirmar').onclick = function() {
            form.submit();
        };
    };

    document.getElementById('modalDetalhe').style.display = 'flex';
}

function fecharDetalhe() {
    document.getElementById('modalDetalhe').style.display = 'none';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharDetalhe();
        fecharModal();
    }
});
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

.modal-detalhe {
    background: #fff;
    border-radius: 20px;
    max-width: 500px;
    width: 90%;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    position: relative;
    animation: zoomIn 0.2s ease;
}

@keyframes zoomIn {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.btn-fechar {
    position: absolute;
    top: 12px;
    right: 16px;
    background: rgba(0,0,0,0.5);
    color: #fff;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    font-size: 20px;
    cursor: pointer;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.btn-fechar:hover { background: rgba(0,0,0,0.7); }

.detalhe-imagem {
    width: 100%;
    aspect-ratio: 1 / 1;
    background: #fafafa;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.detalhe-imagem img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.detalhe-info {
    padding: 24px;
}

.detalhe-info h2 {
    margin: 0 0 8px;
    font-size: 22px;
    color: #2d2d2d;
}

.detalhe-preco {
    font-size: 24px;
    font-weight: 700;
    color: #ff6200;
    margin: 0 0 12px;
}

.detalhe-descricao {
    font-size: 15px;
    color: #666;
    margin: 0 0 8px;
    line-height: 1.5;
}

.detalhe-estoque {
    font-size: 13px;
    color: #999;
    margin: 0 0 20px;
}

.detalhe-acoes {
    display: flex;
    gap: 10px;
}

.detalhe-acoes .btn-card {
    flex: 1;
    padding: 10px 0;
    font-size: 14px;
}

.card {
    cursor: pointer;
}
</style>
