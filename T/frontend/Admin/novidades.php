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
    $titulo = $_POST['titulo'];
    $conteudo = $_POST['conteudo'];
    $conn->query("INSERT INTO novidades (titulo, conteudo) VALUES ('$titulo', '$conteudo')");
}

// Exclusão
if(isset($_GET['del'])){
    $id = $_GET['del'];
    $conn->query("DELETE FROM novidades WHERE id = $id");
}

// Consulta
$novidades = $conn->query("SELECT * FROM novidades");
?>


<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Novidades</title>
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
            <h2>Gerenciar Novidades</h2>
                <form method="post">
                    <input class="form-control mb-2" type="text" name="titulo" placeholder="Título" required>
                    <textarea class="form-control mb-2" name="conteudo" placeholder="Conteúdo" required></textarea>
                    <button class="btn btn-primary" type="submit" name="add">Adicionar</button>
                </form>

            <h3 class="mt-4">Lista de Novidades</h3>
                <ul class="list-group">
                    <?php while($n = $novidades->fetch_assoc()): ?>
                        <li class="list-group-item">
                            <strong><?= $n['titulo'] ?></strong>: <?= $n['conteudo'] ?>
                            <a class="btn btn-sm btn-danger float-end" href="?del=<?= $n['id'] ?>">Excluir</a>
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
