<?php
require "supabaseConnection.php";

$result = supabaseRequest("/rest/v1/produto?select=*");

echo "Code: " . $result['code'] . "\n";
echo "Data type: " . gettype($result['data']) . "\n";
echo "Data: " . print_r($result['data'], true) . "\n";
?>
