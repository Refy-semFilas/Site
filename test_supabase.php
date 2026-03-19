<?php
require "supabaseConnection.php";

echo "<h2>Teste de conexão com Supabase</h2>";

$result = supabaseRequest("/rest/v1/usuarios?select=*");

echo "<p>Código HTTP: " . $result['code'] . "</p>";
echo "<p>Dados: <pre>" . print_r($result['data'], true) . "</pre></p>";

if (isset($result['error'])) {
    echo "<p>Erro: " . $result['error'] . "</p>";
}

if (isset($result['raw'])) {
    echo "<p>Resposta bruta: " . $result['raw'] . "</p>";
}
?>
