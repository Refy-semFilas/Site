<?php

function criarPagamentoMP($accessToken, $valor, $descricao, $emailCliente, $notificationUrl) {
    $url = 'https://api.mercadopago.com/v1/payments';

    $body = [
        'transaction_amount' => round($valor, 2),
        'description' => $descricao,
        'payment_method_id' => 'pix',
        'payer' => [
            'email' => $emailCliente
        ],
        'notification_url' => $notificationUrl
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($response, true);

    if ($httpCode === 201 && isset($data['id'])) {
        return [
            'success' => true,
            'mp_payment_id' => $data['id'],
            'mp_status' => $data['status'],
            'qr_code' => $data['point_of_interaction']['transaction_data']['qr_code'] ?? '',
            'qr_code_base64' => $data['point_of_interaction']['transaction_data']['qr_code_base64'] ?? '',
            'raw' => $data
        ];
    }

    return [
        'success' => false,
        'error' => $data['message'] ?? 'Erro ao criar pagamento',
        'details' => $data
    ];
}

function consultarPagamentoMP($accessToken, $mpPaymentId) {
    $url = 'https://api.mercadopago.com/v1/payments/' . $mpPaymentId;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $data = json_decode($response, true);
        return [
            'success' => true,
            'status' => $data['status'] ?? 'unknown',
            'status_detail' => $data['status_detail'] ?? ''
        ];
    }

    return ['success' => false];
}
