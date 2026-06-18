<?php
session_start();
require "../supabaseConnection.php";
require "../userFunctions.php";

if (!isAdmin()) {
    header("Location: ../../../loginForm.html");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: dashboard.php");
    exit;
}

$result = supabaseRequest("/rest/v1/produto?id=eq.$id&select=*");
$produtos = $result['data'] ?? [];

if (count($produtos) === 0) {
    header("Location: dashboard.php");
    exit;
}

$produto = $produtos[0];

if ((int)$produto['usuario_id'] !== (int)$_SESSION['user_id']) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $valor = str_replace(',', '.', $_POST['valor']);
    $CODIGO_DE_BARRAS = $_POST['CODIGO_DE_BARRAS'];
    $imagem = $_POST['imagem_atual'];

    if (!empty($_FILES['imagem']['name'])) {
        $nomeImagem = uniqid() . "-" . $_FILES['imagem']['name'];
        $ext = strtolower(pathinfo($nomeImagem, PATHINFO_EXTENSION));
        $contentType = $ext === 'png' ? 'image/png' : ($ext === 'gif' ? 'image/gif' : 'image/jpeg');
        $upload = supabaseStorageUpload('produtos', $_FILES['imagem']['tmp_name'], $nomeImagem, $contentType);
        if (!isset($upload['error'])) {
            $imagem = $nomeImagem;
        }
    }

    $updateData = [
        'nome' => $nome,
        'descricao' => $descricao,
        'valor' => floatval($valor),
        'codigo_de_barras' => $CODIGO_DE_BARRAS,
        'imagem' => $imagem
    ];

    supabaseRequest("/rest/v1/produto?id=eq.$id", 'PATCH', $updateData);

    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Produto</title>
    <link rel="icon" href="../../../img/Logo png.png">
    <link rel="stylesheet" href="../../../src/styles/mainHeader.css">
    <link rel="stylesheet" href="../../../src/styles/editProduct.css">
    <link rel="stylesheet" href="../../../src/styles/addProduct.css">

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
                    <input type="text" placeholder="Pesquise aqui...">
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

<div class="conteudo-form">
    <h1 class="titulo">
        Alterar <span>produto</span>
    </h1>

    <form method="post" enctype="multipart/form-data" class="form-produto">

        <div class="linha">
            <div class="campo">
                <label>Nome do produto</label> 
                <div class="input-with-icon">
                    <input type="text" name="nome"
                       value="<?= htmlspecialchars($produto['nome']) ?>" required>
                    <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#000"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 7l9-4 9 4v10l-9 4-9-4V7z" />
                        <path d="M3 7l9 4 9-4" />
                        <path d="M12 3v18" />
                    </svg>
                </div>
            </div>

            <div class="campo">
                <label>Descrição do produto</label>
                <div class="input-with-icon">
                    <input type="text" name="descricao"
                       value="<?= htmlspecialchars($produto['descricao']) ?>" required>
                    <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#000"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <path d="M14 2v6h6" />
                        <path d="M8 12h8" />
                        <path d="M8 16h8" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="linha">
            <div class="campo">
                <label>Valor do produto</label>  
                <div class="input-with-icon">
                    <input type="number" step="0.01" name="valor"
                       value="<?= $produto['valor'] ?>" required>
                    <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#000"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 1v22" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H15a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                </div>
            </div>

            <div class="campo">
                <label>Código de barras</label>
                <div class="input-with-icon">
                    <input type="text" name="CODIGO_DE_BARRAS"
                       value="<?= htmlspecialchars($produto['codigo_de_barras']) ?>" required>
                    <svg class="icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#000"
                        stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="4" width="20" height="16" rx="1" />
                        <path d="M6 8v8" />
                        <path d="M9 6v12" />
                        <path d="M12 8v8" />
                        <path d="M15 6v12" />
                        <path d="M18 8v8" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="imagem-produto">
            <p>Imagem atual</p>
            <img 
                id="previewImagem"
                src="<?= SUPABASE_STORAGE_URL ?>produtos/<?= htmlspecialchars($produto['imagem']) ?>"
                alt="Imagem do produto"
            >
            <label class="add-img">
                    Alterar imagem
                <input 
                    type="file" 
                    name="imagem" 
                    hidden 
                    accept="image/*"
                    onchange="previewNovaImagem(event)"
                >
            </label>
            <input type="hidden" name="imagem_atual"
            value="<?= htmlspecialchars($produto['imagem']) ?>">
        </div>


        <div class="botoes">
            <a href="dashboard.php" class="btn cancelar">Cancelar</a>
            <button type="submit" class="btn adicionar">Salvar alterações</button>
        </div>

    </form>
</div>

    <script>
    function previewNovaImagem(event) {
        const img = document.getElementById('previewImagem');
        const arquivo = event.target.files[0];

        if (arquivo) {
            const reader = new FileReader();

            reader.onload = function(e) {
                img.src = e.target.result;
            };

            reader.readAsDataURL(arquivo);
        }
    }
    </script>

</body>
</html>