
<?php
$host = "localhost";
$user = "root";
$senha = "";
$banco = "ong";

$conn = new mysqli($host, $user, $senha, $banco);

if($conn->connect_error){
    die("Erro de conexão: " . $conn->connect_error);
}
?>
