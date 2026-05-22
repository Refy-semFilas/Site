<?php
require "supabaseConnection.php";
require "mpConfig.php";

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$paymentId = null;

if (isset($data['data']['id'])) {
    $paymentId = $data['data']['id'];
} elseif (isset($_POST['id'])) {
    $paymentId = $_POST['id'];
} elseif (isset($data['id'])) {
    $paymentId = $data['id'];
}

if (!$paymentId) {
    http_response_code(400);
    exit;
}

$pagResult = supabaseRequest("/rest/v1/pagamento?mp_payment_id=eq.$paymentId&select=id,vendedor_id");

if ($pagResult['code'] !== 200 || empty($pagResult['data'])) {
    http_response_code(404);
    exit;
}

$pag = $pagResult['data'][0];

$vendedorResult = supabaseRequest("/rest/v1/usuarios?id=eq.{$pag['vendedor_id']}&select=mp_access_token");

if ($vendedorResult['code'] !== 200 || empty($vendedorResult['data'])) {
    http_response_code(404);
    exit;
}

$token = $vendedorResult['data'][0]['mp_access_token'] ?? '';
if (!$token) {
    http_response_code(404);
    exit;
}

$mpCheck = consultarPagamentoMP($token, $paymentId);

if ($mpCheck['success']) {
    supabaseRequest("/rest/v1/pagamento?mp_payment_id=eq.$paymentId", 'PATCH', [
        'mp_status' => $mpCheck['status']
    ]);

    if ($mpCheck['status'] === 'approved') {
        $vendaResult = supabaseRequest("/rest/v1/pagamento?mp_payment_id=eq.$paymentId&select=venda_id");
        $vendaId = $vendaResult['data'][0]['venda_id'] ?? null;

        if ($vendaId) {
            $allPagResult = supabaseRequest("/rest/v1/pagamento?venda_id=eq.$vendaId&select=mp_status");
            $allApproved = true;
            foreach ($allPagResult['data'] as $p) {
                if ($p['mp_status'] !== 'approved') {
                    $allApproved = false;
                    break;
                }
            }

            if ($allApproved) {
                supabaseRequest("/rest/v1/venda?id=eq.$vendaId", 'PATCH', ['status' => 'pago']);

                $itensResult = supabaseRequest("/rest/v1/itens_venda?venda_id=eq.$vendaId&select=produto_id,quantidade");
                foreach ($itensResult['data'] as $item) {
                    $prodCheck = supabaseRequest("/rest/v1/produto?id=eq.{$item['produto_id']}&select=id,estoque");
                    if ($prodCheck['code'] === 200 && !empty($prodCheck['data'])) {
                        $estoqueAtual = (int) ($prodCheck['data'][0]['estoque'] ?? 0);
                        supabaseRequest("/rest/v1/produto?id=eq.{$item['produto_id']}", 'PATCH', [
                            'estoque' => $estoqueAtual - (int) $item['quantidade']
                        ]);
                    }
                }
            }
        }
    }
}

http_response_code(200);
