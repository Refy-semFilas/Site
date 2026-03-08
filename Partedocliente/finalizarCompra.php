<?php
require "../databaseConnection.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['itens']) || empty($data['itens'])) {
    echo json_encode(['success' => false, 'message' => 'Carrinho vazio']);
    exit;
}

$conn->begin_transaction();
$erro = false;

foreach ($data['itens'] as $item) {
    $produtoId = $item['id'];
    $quantidade = $item['quantidade'];
    
    $check = $conn->prepare("SELECT ESTOQUE FROM produto WHERE id = ?");
    $check->bind_param("i", $produtoId);
    $check->execute();
    $result = $check->get_result();
    $produto = $result->fetch_assoc();
    
    if (!$produto) {
        $erro = true;
        break;
    }
    
    if ($produto['ESTOQUE'] < $quantidade) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Estoque insuficiente para: ' . $item['nome']]);
        exit;
    }
    
    $update = $conn->prepare("UPDATE produto SET ESTOQUE = ESTOQUE - ? WHERE id = ?");
    $update->bind_param("ii", $quantidade, $produtoId);
    if (!$update->execute()) {
        $erro = true;
        break;
    }
}

if ($erro) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Erro ao processar compra']);
} else {
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Compra finalizada com sucesso']);
}
?>
