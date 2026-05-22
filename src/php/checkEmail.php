<?php
require "supabaseConnection.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'] ?? '';
    
    if (empty($email)) {
        echo json_encode(['exists' => false]);
        exit;
    }
    
    $result = supabaseRequest("/rest/v1/usuarios?email=eq.$email&select=id");
    
    echo json_encode(['exists' => count($result['data']) > 0]);
} else {
    echo json_encode(['error' => 'Método não permitido']);
}
?>
