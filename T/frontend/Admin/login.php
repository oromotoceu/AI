<?php
session_start();
include('conexao.php');

if(isset($_POST['login'])){
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    if($usuario == 'admin' && $senha == '123'){
        $_SESSION['logado'] = true;
        header("Location: dashboard.php");
    } else {
        $erro = "Usuário ou senha incorretos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Login - Admin</title>
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
                        <li><a href="login.php">Login</a></li>
                    </ul>
                </nav>
        </header>

        <main class="container mt-5">
            <h2>Login Administrativo</h2>
                <?php if(isset($erro)) echo "<div class='alert alert-danger'>$erro</div>"; ?>
                <form method="post">
                    <input class="form-control mb-2" type="text" name="usuario" placeholder="Usuário" required>
                    <input class="form-control mb-2" type="password" name="senha" placeholder="Senha" required>
                    <button class="btn btn-primary" type="submit" name="login">Entrar</button>
                </form>
        </main>

        <footer class="footer">
            <p> 2025 ONG Ambiental. Todos os direitos reservados - Mundo Verde</p>
        </footer>
    </body>
</html>
