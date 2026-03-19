<?php
require "../supabaseConnection.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['itens']) || empty($data['itens'])) {
    echo json_encode(['success' => false, 'message' => 'Carrinho vazio']);
    exit;
}

foreach ($data['itens'] as $item) {
    $produtoId = $item['id'];
    $quantidade = $item['quantidade'];
    
    $check = supabaseRequest("/rest/v1/produto?id=eq.$produtoId&select=id,estoque");
    
    if (count($check['data']) === 0) {
        echo json_encode(['success' => false, 'message' => 'Produto não encontrado: ' . $produtoId]);
        exit;
    }
    
    $produto = $check['data'][0];
    $novoEstoque = $produto['estoque'] - $quantidade;
    
    if ($novoEstoque < 0) {
        echo json_encode(['success' => false, 'message' => 'Estoque insuficiente para: ' . $item['nome']]);
        exit;
    }
    
    $update = supabaseRequest("/rest/v1/produto?id=eq.$produtoId", 'PATCH', [
        'estoque' => $novoEstoque
    ]);
    
    if ($update['code'] !== 200 && $update['code'] !== 204) {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar estoque']);
        exit;
    }
}

echo json_encode(['success' => true, 'message' => 'Compra finalizada com sucesso']);
?>
