<?php
session_start();
require "supabaseConnection.php";
require "mpConfig.php";

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$paymentIds = $input['payment_ids'] ?? [];

if (empty($paymentIds)) {
    echo json_encode(['success' => false]);
    exit;
}

$result = supabaseRequest(
    "/rest/v1/pagamento?mp_payment_id=in.(" . implode(',', $paymentIds) . ")&select=id,mp_payment_id,mp_status,vendedor_id,mp_access_token"
);

if ($result['code'] !== 200 || empty($result['data'])) {
    echo json_encode(['success' => false]);
    exit;
}

$allApproved = true;
$payments = [];

foreach ($result['data'] as $pag) {
    $status = $pag['mp_status'] ?? 'pending';

    if ($status === 'pending' || $status === 'in_process') {
        $vendedorResult = supabaseRequest("/rest/v1/usuarios?id=eq.{$pag['vendedor_id']}&select=mp_access_token");
        $token = $vendedorResult['data'][0]['mp_access_token'] ?? '';
        if (!$token) {
            $token = getenv('MP_ACCESS_TOKEN') ?: '';
        }

        if ($token) {
            $mpCheck = consultarPagamentoMP($token, $pag['mp_payment_id']);
            if ($mpCheck['success']) {
                $status = $mpCheck['status'];
                supabaseRequest("/rest/v1/pagamento?id=eq.{$pag['id']}", 'PATCH', [
                    'mp_status' => $status
                ]);
            }
        }
    }

    $payments[] = [
        'mp_payment_id' => $pag['mp_payment_id'],
        'status' => $status
    ];

    if ($status !== 'approved') {
        $allApproved = false;
    }
}

echo json_encode([
    'success' => true,
    'all_approved' => $allApproved,
    'payments' => $payments
]);
