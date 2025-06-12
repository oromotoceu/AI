<?php
session_start();

$inactive = 120; // 2 minutos

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $inactive)) {
    // Tempo inativo excedido
    session_unset();     // limpa variáveis
    session_destroy();   // destrói sessão
    header("Location: login.php?msg=Sessão expirada. Faça login novamente.");
    exit();
}

$_SESSION['LAST_ACTIVITY'] = time(); // atualiza último tempo de atividade

if(!isset($_SESSION['logado'])){header("Location: login.php?msg=Acesso restrito, faça login.");exit();}
include('conexao.php');
?>


<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Administração</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    </head>

    <body>
        <header>
            <h1>Mundo Verde</h1>
                <nav class="nav">
                    <ul>
                        <li><a href="../index.php">Início</a></li>
                        <li><a href="../sobre.php">Sobre</a></li>
                        <li><a href="../produto.php">Projetos</a></li>
                        <li><a href="../novidade.php">Novidades</a></li>
                        <li><a href="logout.php">Sair</a></li>
                    </ul>
                </nav>
        </header>

        <main>
            <div class="box-texto-img">
                <h2>Painel Administrativo</h2>
                    <a class="btn btn-secondary" href="produtos.php">Gerenciar Produtos</a>
                    <a class="btn btn-secondary" href="novidades.php">Gerenciar Novidades</a>
                    <a class="btn btn-secondary" href="usuarios.php">Gerenciar Usuários</a>
            </div>
        </main>

        <footer class="footer">
            <p> 2025 ONG Ambiental. Todos os direitos reservados - Mundo Verde</p>
        </footer>

    </body>
</html>