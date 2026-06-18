<?php
/**
 * Seed products from imgBD/ into Supabase
 * Run via browser: http://localhost/Site/src/php/seedProducts.php
 * Or via CLI: php src/php/seedProducts.php
 */

require_once __DIR__ . "/supabaseConnection.php";

echo "=== Iniciando seed de produtos ===\n\n";

// 1. Encontrar ou criar admin
$usuario_id = getOrCreateAdminUser();
echo "Usando usuario_id: $usuario_id\n\n";

// 2. Produtos mapeados a partir das imagens em imgBD/
$products = [
    // Bebidas
    [
        'nome' => 'Coca-Cola',
        'descricao' => 'Refrigerante Coca-Cola 350ml',
        'valor' => 5.00,
        'categoria' => 'bebidas',
        'arquivo' => '695b05b9754e0-bebid.jpg',
        'contentType' => 'image/jpeg',
        'estoque' => 50,
    ],
    [
        'nome' => 'Suco de Laranja',
        'descricao' => 'Suco natural de laranja 300ml',
        'valor' => 6.00,
        'categoria' => 'bebidas',
        'arquivo' => '69cc1e1399c1d-bebida.webp',
        'contentType' => 'image/webp',
        'estoque' => 40,
    ],
    // Doces
    [
        'nome' => 'Brigadeiro',
        'descricao' => 'Brigadeiro caseiro delicioso',
        'valor' => 3.50,
        'categoria' => 'doces',
        'arquivo' => '695b066079e69-doce.jpg',
        'contentType' => 'image/jpeg',
        'estoque' => 30,
    ],
    [
        'nome' => 'Beijinho',
        'descricao' => 'Beijinho de coco tradicional',
        'valor' => 3.50,
        'categoria' => 'doces',
        'arquivo' => '695b05a3c73e0-doce.jpg',
        'contentType' => 'image/jpeg',
        'estoque' => 30,
    ],
    [
        'nome' => 'Bolo de Chocolate',
        'descricao' => 'Fatia de bolo de chocolate',
        'valor' => 7.00,
        'categoria' => 'doces',
        'arquivo' => '69bb4fa13260f-bolo.jpg',
        'contentType' => 'image/jpeg',
        'estoque' => 20,
    ],
    [
        'nome' => 'Bolo de Cenoura',
        'descricao' => 'Fatia de bolo de cenoura com cobertura',
        'valor' => 7.00,
        'categoria' => 'doces',
        'arquivo' => '69bb4f82a8553-bolo.jpg',
        'contentType' => 'image/jpeg',
        'estoque' => 20,
    ],
    [
        'nome' => 'Fatia de Bolo',
        'descricao' => 'Fatia de bolo caseiro',
        'valor' => 6.00,
        'categoria' => 'doces',
        'arquivo' => '69bb4ced3219f-bolo.jpg',
        'contentType' => 'image/jpeg',
        'estoque' => 20,
    ],
    // Salgados
    [
        'nome' => 'Coxinha',
        'descricao' => 'Coxinha de frango',
        'valor' => 6.00,
        'categoria' => 'salgados',
        'arquivo' => '695b059138f5c-salgado.jpg',
        'contentType' => 'image/jpeg',
        'estoque' => 25,
    ],
    [
        'nome' => 'Pizza de Calabresa',
        'descricao' => 'Pizza de calabresa',
        'valor' => 8.00,
        'categoria' => 'salgados',
        'arquivo' => '69bb29272dfa7-pizza.jpg',
        'contentType' => 'image/jpeg',
        'estoque' => 15,
    ],
    [
        'nome' => 'Pizza de Queijo',
        'descricao' => 'Pizza de queijo',
        'valor' => 8.00,
        'categoria' => 'salgados',
        'arquivo' => '69bb2a04ead2a-pizza.jpg',
        'contentType' => 'image/jpeg',
        'estoque' => 15,
    ],
    [
        'nome' => 'Pizza de Frango',
        'descricao' => 'Pizza de frango com catupiry',
        'valor' => 9.00,
        'categoria' => 'salgados',
        'arquivo' => '69bb4c29acff4-pizza.jpg',
        'contentType' => 'image/jpeg',
        'estoque' => 15,
    ],
    [
        'nome' => 'Pizza Margherita',
        'descricao' => 'Pizza margherita tradicional',
        'valor' => 8.50,
        'categoria' => 'salgados',
        'arquivo' => '69bb4ebc9809b-pizza.jpg',
        'contentType' => 'image/jpeg',
        'estoque' => 15,
    ],
    [
        'nome' => 'Pizza Portuguesa',
        'descricao' => 'Pizza portuguesa',
        'valor' => 9.00,
        'categoria' => 'salgados',
        'arquivo' => '69bb4f60948c9-pizza.jpg',
        'contentType' => 'image/jpeg',
        'estoque' => 15,
    ],
    // Imagens genéricas (download)
    [
        'nome' => 'Salgado Assado',
        'descricao' => 'Salgado assado',
        'valor' => 5.00,
        'categoria' => 'salgados',
        'arquivo' => '698a6f2f0246c-download.jpg',
        'contentType' => 'image/jpeg',
        'estoque' => 20,
    ],
    [
        'nome' => 'Lanche Natural',
        'descricao' => 'Lanche natural',
        'valor' => 7.00,
        'categoria' => 'salgados',
        'arquivo' => '698a6f4248da9-download.jpg',
        'contentType' => 'image/jpeg',
        'estoque' => 20,
    ],
    [
        'nome' => 'Torta Salgada',
        'descricao' => 'Torta salgada',
        'valor' => 6.00,
        'categoria' => 'salgados',
        'arquivo' => '698a6f5048c71-download.jpg',
        'contentType' => 'image/jpeg',
        'estoque' => 15,
    ],
    [
        'nome' => 'Bebida Natural',
        'descricao' => 'Bebida natural 500ml',
        'valor' => 5.00,
        'categoria' => 'bebidas',
        'arquivo' => '698a71d0c2f68-download.jpg',
        'contentType' => 'image/jpeg',
        'estoque' => 35,
    ],
    [
        'nome' => 'Doce Caseiro',
        'descricao' => 'Doce caseiro especial',
        'valor' => 4.00,
        'categoria' => 'doces',
        'arquivo' => '698a71df2951c-download.jpg',
        'contentType' => 'image/jpeg',
        'estoque' => 25,
    ],
];

$imgDir = __DIR__ . '/../../imgBD/';
$total = 0;
$errors = [];

foreach ($products as $p) {
    $filePath = $imgDir . $p['arquivo'];

    if (!file_exists($filePath)) {
        $errors[] = "Arquivo nao encontrado: {$p['arquivo']}";
        continue;
    }

    // Upload image to Supabase Storage
    echo "Enviando imagem: {$p['arquivo']}... ";
    $upload = supabaseStorageUpload('produtos', $filePath, $p['arquivo'], $p['contentType']);

    if (isset($upload['error'])) {
        echo " ERRO: {$upload['error']}\n";
        $errors[] = "Falha ao enviar {$p['arquivo']}: {$upload['error']}";
        continue;
    }
    echo "OK\n";

    // Generate unique barcode
    $codigoBarras = '789' . str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);

    // Insert product record
    echo "Inserindo: {$p['nome']}... ";
    $insert = supabaseRequest("/rest/v1/produto", 'POST', [
        'nome' => $p['nome'],
        'descricao' => $p['descricao'],
        'valor' => $p['valor'],
        'codigo_de_barras' => $codigoBarras,
        'imagem' => $p['arquivo'],
        'categoria' => $p['categoria'],
        'estoque' => $p['estoque'],
        'usuario_id' => $usuario_id,
    ]);

    if ($insert['code'] === 201) {
        echo "OK (codigo: $codigoBarras)\n";
        $total++;
    } else {
        echo "ERRO: HTTP {$insert['code']}\n";
        $errorMsg = $insert['error'] ?? json_encode($insert['data']);
        $errors[] = "Falha ao inserir {$p['nome']}: $errorMsg";
    }
}

echo "\n=== Seed concluido! ===\n";
echo "Produtos cadastrados: $total\n";

if (count($errors) > 0) {
    echo "\nErros encontrados:\n";
    foreach ($errors as $e) {
        echo "  - $e\n";
    }
}

// ---------------------------------------------------------------------------

function getOrCreateAdminUser() {
    // Try to find an existing admin
    $result = supabaseRequest("/rest/v1/usuarios?tipo=eq.admin&select=id&limit=1");
    if (!empty($result['data'])) {
        return (int)$result['data'][0]['id'];
    }

    // Try any user
    $result = supabaseRequest("/rest/v1/usuarios?select=id&limit=1");
    if (!empty($result['data'])) {
        echo "Atencao: nenhum admin encontrado, usando usuario ID {$result['data'][0]['id']}\n";
        return (int)$result['data'][0]['id'];
    }

    // Create a default admin
    echo "Criando usuario admin padrao (login: admin / senha: admin123)...\n";
    $senhaHash = password_hash('admin123', PASSWORD_DEFAULT);
    $insert = supabaseRequest("/rest/v1/usuarios", 'POST', [
        'username' => 'admin',
        'email' => 'admin@cantina.com',
        'senha' => $senhaHash,
        'tipo' => 'admin',
    ]);

    if ($insert['code'] === 201 && !empty($insert['data'])) {
        echo "Admin criado com ID: {$insert['data'][0]['id']}\n";
        return (int)$insert['data'][0]['id'];
    }

    die("ERRO: Nao foi possivel criar usuario admin. Resposta: " . json_encode($insert) . "\n");
}
