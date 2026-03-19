<?php
$supabaseUrl = 'https://myojsyuijqveviwfpjqf.supabase.co';
$supabaseKey = 'sb_secret_qizh8h_ej1sFYphJf4SOFA_VervhwEs';

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

function alerta($mensagem, $redirect = null, $voltar = false)
{
    echo "
    <html>
    <head>
    <style>
        body {
            margin:0;
            font-family: Arial, sans-serif;
            background: transparent;
            display: flex;
            justify-content: center;
            margin-top: 30px
        }

        .alert-box {
            background: linear-gradient(135deg, #ff9100, #ff5e00);
            color: white;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
            font-size: 16px;
            animation: slide 0.4s ease;
            height: 40px;
            width:200px;
            display: flex;
            justify-content: center;
            align-items: center;

        }

        @keyframes slide {
            from {
                opacity: 0;
                transform: translateX(50px);
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
