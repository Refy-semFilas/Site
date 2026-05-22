<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Faça login para finalizar a compra']);
    exit;
}

require "supabaseConnection.php";
require "mpConfig.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data) || !isset($data['itens']) || !is_array($data['itens']) || empty($data['itens'])) {
    echo json_encode(['success' => false, 'message' => 'Carrinho vazio']);
    exit;
}

$emailCliente = $_SESSION['email'] ?? 'cliente@email.com';
$clienteId = $_SESSION['user_id'];

$totalGeral = 0;
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

    if ($estoqueAtual - $quantidade < 0) {
        echo json_encode(['success' => false, 'message' => 'Estoque insuficiente para: ' . ($item['nome'] ?? 'produto')]);
        exit;
    }

    $preco = (float) ($item['preco'] ?? 0);
    $totalGeral += $preco * $quantidade;

    if ($usuarioId) {
        $vendors[$usuarioId] = true;
        if (!isset($totalPorVendedor[$usuarioId])) {
            $totalPorVendedor[$usuarioId] = 0;
        }
        $totalPorVendedor[$usuarioId] += $preco * $quantidade;
    }
}

$vendaData = [
    'cliente_id' => $clienteId,
    'data' => date('Y-m-d'),
    'status' => 'aguardando'
];

$vendaResult = supabaseRequest("/rest/v1/venda", 'POST', $vendaData);

if ($vendaResult['code'] !== 201) {
    echo json_encode(['success' => false, 'message' => 'Erro ao criar pedido']);
    exit;
}

$vendaId = $vendaResult['data'][0]['id'] ?? null;

if (!$vendaId) {
    echo json_encode(['success' => false, 'message' => 'Erro ao obter ID do pedido']);
    exit;
}

foreach ($data['itens'] as $item) {
    supabaseRequest("/rest/v1/itens_venda", 'POST', [
        'venda_id' => $vendaId,
        'produto_id' => (int) $item['id'],
        'quantidade' => (int) $item['quantidade'],
        'preco_unitario' => (float) ($item['preco'] ?? 0)
    ]);
}

$siteUrl = getenv('SITE_URL') ?: 'http://localhost/Site';
$notificationUrl = rtrim($siteUrl, '/') . '/src/php/webhook.php';

$pixData = [];
$pagamentosCriados = 0;

if (!empty($vendors)) {
    $ids = array_keys($vendors);
    $filters = [];
    foreach ($ids as $id) {
        $filters[] = "id.eq.$id";
    }
    $filterString = 'or=(' . implode(',', $filters) . ')';
    $result = supabaseRequest("/rest/v1/usuarios?" . $filterString . "&select=id,username,chave_pix,mp_access_token,email");

    if ($result['code'] === 200 && !empty($result['data'])) {
        foreach ($result['data'] as $v) {
            $total = $totalPorVendedor[$v['id']] ?? 0;
            $accessToken = $v['mp_access_token'] ?? '';

            if (empty($accessToken)) {
                $pixData[] = [
                    'vendedor' => $v['username'],
                    'erro' => 'Vendedor sem token MP configurado',
                    'valor' => $total
                ];
                continue;
            }

            $mpResult = criarPagamentoMP(
                $accessToken,
                $total,
                'Pedido #' . $vendaId . ' - ' . $v['username'],
                $emailCliente,
                $notificationUrl
            );

            if ($mpResult['success']) {
                supabaseRequest("/rest/v1/pagamento", 'POST', [
                    'venda_id' => $vendaId,
                    'mp_payment_id' => $mpResult['mp_payment_id'],
                    'mp_status' => $mpResult['mp_status'],
                    'valor' => $total,
                    'vendedor_id' => $v['id'],
                    'qr_code' => $mpResult['qr_code'],
                    'qr_code_base64' => $mpResult['qr_code_base64']
                ]);

                $pixData[] = [
                    'vendedor' => $v['username'],
                    'valor' => $total,
                    'qr_code' => $mpResult['qr_code'],
                    'qr_code_base64' => $mpResult['qr_code_base64'],
                    'mp_payment_id' => $mpResult['mp_payment_id']
                ];
                $pagamentosCriados++;
            } else {
                $pixData[] = [
                    'vendedor' => $v['username'],
                    'erro' => $mpResult['error'],
                    'valor' => $total
                ];
            }
        }
    }
}

if ($pagamentosCriados === 0) {
    supabaseRequest("/rest/v1/venda?id=eq.$vendaId", 'PATCH', ['status' => 'erro']);
    echo json_encode(['success' => false, 'message' => 'Nenhum pagamento pôde ser criado. Verifique os tokens MP dos vendedores.']);
    exit;
}

echo json_encode([
    'success' => true,
    'venda_id' => $vendaId,
    'pix' => $pixData,
    'total' => $totalGeral
]);
