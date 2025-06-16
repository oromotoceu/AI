<?php
session_start(); // Garante que a sessão seja iniciada

// Limpa todas as variáveis de sessão relacionadas ao usuário do site
unset($_SESSION['usuario_logado_id']);
unset($_SESSION['usuario_logado_nome']);
unset($_SESSION['usuario_logado_email']);
unset($_SESSION['LAST_ACTIVITY']); // Se você usa para ambos, pode precisar de uma lógica mais específica

// Redireciona para a página inicial
header("Location: index.php");
exit();
?>