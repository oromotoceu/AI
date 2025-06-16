<?php
// Inclui o header.php que já inicia a sessão e o <head>/<header>
include 'header.php'; 

include('Admin/conexao.php'); // Reutiliza a conexão

// Verifica se já está logado como admin e redireciona (se for o caso)
if (isset($_SESSION['logado'])) {
    header("Location: Admin/dashboard.php"); // Redireciona para o painel admin se já logado como admin
    exit();
}

$erro = '';

if(isset($_POST['login_usuario'])){
    $email_digitado = $_POST['email'];
    $senha_digitada = $_POST['senha'];

    $stmt = $conn->prepare("SELECT id, nome, email, senha FROM usuarios_site WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email_digitado);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            if (password_verify($senha_digitada, $usuario['senha'])) {
                $_SESSION['usuario_logado_id'] = $usuario['id']; // ID do usuário logado
                $_SESSION['usuario_logado_nome'] = $usuario['nome']; // Nome do usuário
                $_SESSION['usuario_logado_email'] = $usuario['email']; // Email do usuário
                $_SESSION['LAST_ACTIVITY'] = time(); // Atualiza tempo de atividade
                header("Location: index.php"); // Redireciona para a página inicial
                exit();
            } else {
                $erro = "Email ou senha incorretos!";
            }
        } else {
            $erro = "Email ou senha incorretos!";
        }
        $stmt->close();
    } else {
        $erro = "Erro interno no servidor de autenticação.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
    <body class="d-flex flex-column min-vh-100">
        <main class="flex-grow-1 d-flex justify-content-center align-items-center py-5">
            <div class="card p-4 shadow-lg" style="max-width: 450px; width: 100%;">
                <h2 class="card-title text-center mb-4 text-primary">Login de Usuário</h2>
                <?php if(!empty($erro)) echo "<div class='alert alert-danger'>$erro</div>"; ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input class="form-control" type="email" id="email" name="email" placeholder="Seu email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input class="form-control" type="password" id="senha" name="senha" placeholder="Sua senha" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit" name="login_usuario">Entrar</button>
                </form>
                <p class="mt-3 text-center">Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
            </div>
        </main>

        <footer>
            <p> 2025 ONG Ambiental - Todos os direitos reservados - Mundo Verde</p>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>