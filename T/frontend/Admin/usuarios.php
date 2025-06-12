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

// Inserção
if(isset($_POST['add'])){
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $conn->query("INSERT INTO usuarios (nome, email, senha) VALUES ('$nome', '$email', '$senha')");
}

// Exclusão
if(isset($_GET['del'])){
    $id = $_GET['del'];
    $conn->query("DELETE FROM usuarios WHERE id = $id");
}

// Consulta
$usuarios = $conn->query("SELECT * FROM usuarios");
?>


<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Usuários</title>
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

        <main class="container mt-5">
            <h2>Adicionar Usuários</h2>
                <form method="post">
                    <input class="form-control mb-2" type="text" name="nome" placeholder="Nome" required>
                    <input class="form-control mb-2" type="email" name="email" placeholder="email" required>
                    <input class="form-control mb-2" type="password" name="senha" placeholder="senha" required>
                    <button class="btn btn-primary" type="submit" name="add">Salvar</button>
                </form>

            <h3 class="mt-4">Lista de Usuários</h3>
                <ul class="list-group">
                    <?php while($p = $usuarios->fetch_assoc()): ?>
                        <li class="list-group-item">
                            <strong><?= $p['nome'] ?></strong>: <?= $p['email'] ?>
                            <a class="btn btn-sm btn-danger float-end" href="?del=<?= $p['id'] ?>">Excluir</a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <a class="btn btn-secondary mt-3" href="dashboard.php">Voltar</a>
        </main>

        <footer class="footer">
            <p> 2025 ONG Ambiental. Todos os direitos reservados - Mundo Verde</p>
        </footer>
    </body>
</html>