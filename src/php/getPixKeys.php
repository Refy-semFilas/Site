<?php
require "supabaseConnection.php";

header('Content-Type: application/json');

$ids = $_GET['ids'] ?? '';

if (empty($ids)) {
    echo json_encode([]);
    exit;
}

$idsArray = array_map('intval', explode(',', $ids));
$filters = [];
foreach ($idsArray as $id) {
    $filters[] = "id=eq.$id";
}
$filterString = 'or=(' . implode(',', $filters) . ')';

$result = supabaseRequest("/rest/v1/usuarios?" . $filterString . "&select=id,username,chave_pix");
echo json_encode($result['data'] ?? []);
