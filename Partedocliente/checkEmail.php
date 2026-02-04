<?php
require "../databaseConnection.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'] ?? '';
    
    if (empty($email)) {
        echo json_encode(['exists' => false]);
        exit;
    }
    
    // Verificar se o email já existe no banco
    $sql = $conn->prepare("SELECT ID FROM usuarios WHERE EMAIL = ?");
    $sql->bind_param("s", $email);
    $sql->execute();
    $result = $sql->get_result();
    
    echo json_encode(['exists' => $result->num_rows > 0]);
} else {
    echo json_encode(['error' => 'Método não permitido']);
}
?>