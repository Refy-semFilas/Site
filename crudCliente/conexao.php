<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "cantina";

$conexao = mysqli_connect($host, $user, $pass, $db);

if(!$conexao){
    die("Erro na conexão: " . mysqli_connect_error());
}
?>