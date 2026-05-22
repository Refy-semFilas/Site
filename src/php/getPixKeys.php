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
if ($result['code'] === 400) {
    $result = supabaseRequest("/rest/v1/usuarios?" . $filterString . "&select=id,username");
    $data = array_map(function($u) {
        $u['chave_pix'] = null;
        return $u;
    }, $result['data'] ?? []);
    echo json_encode($data);
} else {
    echo json_encode($result['data'] ?? []);
}
