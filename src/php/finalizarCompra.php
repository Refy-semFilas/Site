<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Faça login para finalizar a compra']);
    exit;
}

require "supabaseConnection.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data) || !isset($data['itens']) || !is_array($data['itens']) || empty($data['itens'])) {
    echo json_encode(['success' => false, 'message' => 'Carrinho vazio']);
    exit;
}

$vendors = [];
$totalPorVendedor = [];

foreach ($data['itens'] as $item) {
    if (!isset($item['id'], $item['quantidade'])) {
        echo json_encode(['success' => false, 'message' => 'Item inválido no carrinho']);
        exit;
    }

    $produtoId = $item['id'];
    $quantidade = (int) $item['quantidade'];
    $usuarioId = $item['usuario_id'] ?? null;

    $check = supabaseRequest("/rest/v1/produto?id=eq.$produtoId&select=id,estoque");
    
    if ($check['code'] !== 200 || count($check['data']) === 0) {
        echo json_encode(['success' => false, 'message' => 'Produto não encontrado']);
        exit;
    }
    
    $produto = $check['data'][0];
    $estoqueAtual = (int) ($produto['estoque'] ?? 0);
    $novoEstoque = $estoqueAtual - $quantidade;
    
    if ($novoEstoque < 0) {
        echo json_encode(['success' => false, 'message' => 'Estoque insuficiente para: ' . ($item['nome'] ?? 'produto')]);
        exit;
    }
    
    $update = supabaseRequest("/rest/v1/produto?id=eq.$produtoId", 'PATCH', [
        'estoque' => $novoEstoque
    ]);
    
    if ($update['code'] !== 200 && $update['code'] !== 201 && $update['code'] !== 204) {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar estoque']);
        exit;
    }

    if ($usuarioId) {
        $vendors[$usuarioId] = true;
        $preco = (float) ($item['preco'] ?? 0);
        if (!isset($totalPorVendedor[$usuarioId])) {
            $totalPorVendedor[$usuarioId] = 0;
        }
        $totalPorVendedor[$usuarioId] += $preco * $quantidade;
    }
}

$pixData = [];
if (!empty($vendors)) {
    $ids = array_keys($vendors);
    $filters = [];
    foreach ($ids as $id) {
        $filters[] = "id.eq.$id";
    }
    $filterString = 'or=(' . implode(',', $filters) . ')';
    $result = supabaseRequest("/rest/v1/usuarios?" . $filterString . "&select=id,username,chave_pix");

    if ($result['code'] === 200 && !empty($result['data'])) {
        foreach ($result['data'] as $v) {
            $chave = $v['chave_pix'] ?? '';
            $total = $totalPorVendedor[$v['id']] ?? 0;
            $brCode = '';
            $qrBase64 = '';
            if (!empty($chave)) {
                $brCode = gerarPixCopiaECola($chave, $v['username'], 'Cidade', $total);
                $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($brCode);
                $qrImage = @file_get_contents($qrUrl);
                if ($qrImage !== false) {
                    $qrBase64 = 'data:image/png;base64,' . base64_encode($qrImage);
                }
            }
            $pixData[] = [
                'vendedor' => $v['username'],
                'chave_pix' => $chave,
                'valor' => $total,
                'brcode' => $brCode,
                'qr_base64' => $qrBase64
            ];
        }
    }
}

echo json_encode([
    'success' => true,
    'message' => 'Compra finalizada com sucesso',
    'pix' => $pixData
]);
?>
