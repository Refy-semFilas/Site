<?php
$supabaseUrl = getenv('SUPABASE_URL') ?: 'https://myojsyuijqveviwfpjqf.supabase.co';
$supabaseKey = getenv('SUPABASE_KEY') ?: 'sb_publishable_i1B4kDjelQULBUfro9G-pg_-RhFWkss';
$supabaseServiceKey = getenv('SUPABASE_SERVICE_KEY') ?: 'sb_secret_qizh8h_ej1sFYphJf4SOFA_VervhwEs';

function supabaseRequest($endpoint, $method = 'GET', $data = null, $queryParams = []) {
    global $supabaseUrl, $supabaseKey;
    
    $url = $supabaseUrl . $endpoint;
    
    if (!empty($queryParams)) {
        $url .= '?' . http_build_query($queryParams);
    }
    
    $ch = curl_init($url);
    
    $headers = [
        'apikey: ' . $supabaseKey,
        'Authorization: Bearer ' . $supabaseKey,
        'Content-Type: application/json',
        'Prefer: return=representation'
    ];
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    
    if ($method !== 'GET') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        return [
            'code' => 0,
            'data' => [],
            'error' => $curlError
        ];
    }
    
    $decoded = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE && $response !== '[]' && $response !== '') {
        return [
            'code' => $httpCode,
            'data' => [],
            'error' => json_last_error_msg(),
            'raw' => $response
        ];
    }
    
    return [
        'code' => $httpCode,
        'data' => $decoded ?? []
    ];
}

function gerarPixCopiaECola($chavePix, $nome, $cidade = 'Cidade', $valor = null) {
    $payload = '000201';

    $gui = '0014BR.GOV.BCB.PIX';
    $key = '01' . str_pad(strlen($chavePix), 2, '0', STR_PAD_LEFT) . $chavePix;
    $merchantAccount = $gui . $key;
    $payload .= '26' . str_pad(strlen($merchantAccount), 2, '0', STR_PAD_LEFT) . $merchantAccount;

    $payload .= '52040000';
    $payload .= '5303986';

    if ($valor !== null && $valor > 0) {
        $valorStr = number_format($valor, 2, '.', '');
        $payload .= '54' . str_pad(strlen($valorStr), 2, '0', STR_PAD_LEFT) . $valorStr;
    }

    $payload .= '5802BR';

    $nomeLimpo = substr(trim($nome), 0, 25);
    $payload .= '59' . str_pad(strlen($nomeLimpo), 2, '0', STR_PAD_LEFT) . $nomeLimpo;

    $cidadeLimpo = substr(trim($cidade), 0, 15);
    $payload .= '60' . str_pad(strlen($cidadeLimpo), 2, '0', STR_PAD_LEFT) . $cidadeLimpo;

    $txid = '***';
    $txidBlock = '05' . str_pad(strlen($txid), 2, '0', STR_PAD_LEFT) . $txid;
    $payload .= '62' . str_pad(strlen($txidBlock), 2, '0', STR_PAD_LEFT) . $txidBlock;

    $payload .= '6304';
    $payload .= strtoupper(dechex(crc16Checksum($payload)));

    return $payload;
}

function crc16Checksum($data) {
    $crc = 0xFFFF;
    for ($i = 0; $i < strlen($data); $i++) {
        $crc ^= ord($data[$i]);
        for ($j = 0; $j < 8; $j++) {
            if ($crc & 1) {
                $crc = ($crc >> 1) ^ 0x8408;
            } else {
                $crc >>= 1;
            }
        }
    }
    return $crc ^ 0xFFFF;
}

define('SUPABASE_STORAGE_URL', $supabaseUrl . '/storage/v1/object/public/');

function supabaseStorageUpload($bucket, $filePath, $fileName, $contentType) {
    global $supabaseUrl, $supabaseKey, $supabaseServiceKey;

    $url = $supabaseUrl . "/storage/v1/object/$bucket/$fileName";

    $fileData = file_get_contents($filePath);
    if ($fileData === false) {
        return ['error' => 'Erro ao ler o arquivo'];
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fileData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'apikey: ' . $supabaseServiceKey,
        'Authorization: Bearer ' . $supabaseServiceKey,
        'Content-Type: ' . $contentType
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return ['error' => $curlError];
    }

    $decoded = json_decode($response, true);
    return [
        'code' => $httpCode,
        'data' => $decoded ?? [],
        'path' => "$bucket/$fileName"
    ];
}

function supabaseStorageDelete($bucket, $fileName) {
    global $supabaseUrl, $supabaseKey;

    $url = $supabaseUrl . "/storage/v1/object/$bucket/$fileName";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $supabaseKey
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['code' => $httpCode];
}

function alerta($mensagem, $redirect = null, $voltar = false)
{
    $bg = '#ff6200';
    if (strpos($mensagem, 'sucesso') !== false) $bg = '#46CF6E';
    elseif (strpos($mensagem, 'Erro') !== false || strpos($mensagem, 'erro') !== false) $bg = '#e74c3c';

    echo "
    <html>
    <head>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .alert-box {
            background: $bg;
            color: white;
            padding: 16px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            font-size: 14px;
            max-width: 300px;
            text-align: center;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
    </head>

    <body>
        <div class='alert-box'>$mensagem</div>

        <script>
            setTimeout(() => {";

    if ($redirect) {
        echo "window.location.href = '$redirect';";
    }

    if ($voltar) {
        echo "window.history.back();";
    }

    echo "
            }, 2500);
        </script>
    </body>
    </html>
    ";
    exit;
}
?>
