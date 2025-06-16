<?php
session_start(); // Garante que a sessão seja iniciada para destruí-la
session_destroy();
header("Location: ../index.php"); // Redireciona para a página inicial pública (subindo um nível)
exit();
?>