<?php
// Inclui o header.php que já inicia a sessão e o <head>/<header>
include '../header.php'; // Caminho correto para header.php a partir de Admin/

include('conexao.php'); // conexao.php está na mesma pasta Admin/

// Verifica se já está logado como usuário do site e redireciona (se for o caso)
if (isset($_SESSION['usuario_logado_id'])) {
    header("Location: ../index.php"); // Redireciona para a página inicial se já logado como usuário
    exit();
}

if(isset($_POST['login'])){
    $usuario_digitado = $_POST['usuario'];
    $senha_digitada = $_POST['senha'];

    // Autenticação para o usuário FIXO 'admin' com senha '123'
    if($usuario_digitado == 'admin' && $senha_digitada == '123'){
        $_SESSION['logado'] = true; // Flag de login ADMIN
        $_SESSION['admin_id'] = 'fixed_admin'; 
        $_SESSION['admin_nome'] = 'Administrador Fixo';
        header("Location: dashboard.php");
        exit();
    } else {
        // Autenticação contra a tabela 'administradores' para outros admins
        $stmt = $conn->prepare("SELECT id, nome, usuario, senha FROM administradores WHERE usuario = ?");
        if ($stmt) {
            $stmt->bind_param("s", $usuario_digitado);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $admin = $result->fetch_assoc();
                // Verificar senha hashada
                if (password_verify($senha_digitada, $admin['senha'])) {
                    $_SESSION['logado'] = true; // Flag de login ADMIN
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_nome'] = $admin['nome'];
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $erro = "Usuário ou senha incorretos!";
                }
            } else {
                $erro = "Usuário ou senha incorretos!";
            }
            $stmt->close();
        } else {
            $erro = "Erro interno no servidor de autenticação.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
    <body class="d-flex flex-column min-vh-100">
        <main class="flex-grow-1 d-flex justify-content-center align-items-center py-5">
            <div class="card p-4 shadow-lg" style="max-width: 450px; width: 100%;">
                <h2 class="card-title text-center mb-4 text-primary">Login Administrativo</h2>
                <?php if(isset($erro)) echo "<div class='alert alert-danger'>$erro</div>"; ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuário</label>
                        <input class="form-control" type="text" id="usuario" name="usuario" placeholder="Seu usuário" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input class="form-control" type="password" id="senha" name="senha" placeholder="Sua senha" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit" name="login">Entrar</button>
                </form>
            </div>
        </main>

        <footer>
            <p> 2025 ONG Ambiental - Todos os direitos reservados - Mundo Verde</p>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>