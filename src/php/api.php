<?php
require "supabaseConnection.php";

header('Content-Type: application/json');

$categoria = $_GET['categoria'] ?? null;

$queryParams = ['select' => 'id,nome,descricao,valor,imagem,categoria,estoque,usuario_id'];

if ($categoria) {
    $queryParams['categoria'] = "eq.$categoria";
}

$result = supabaseRequest("/rest/v1/produto", 'GET', null, $queryParams);

$produtos = $result['data'] ?? [];

foreach ($produtos as &$p) {
    if (!empty($p['imagem'])) {
        $p['imagem'] = SUPABASE_STORAGE_URL . 'produtos/' . $p['imagem'];
    }
}

echo json_encode($produtos);