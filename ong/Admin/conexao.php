<?php
$host = "localhost";
$user = "root";
$senha = ""; // Se você definiu uma senha, coloque-a aqui!
$banco = "ong";
$port = "3306";

$conn = new mysqli($host, $user, $senha, $banco, $port);

if($conn->connect_error){
    die("Erro de conexão: " . $conn->connect_error);
}
?>