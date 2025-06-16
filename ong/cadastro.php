<?php
// Inclui o header.php que já inicia a sessão e o <head>/<header>
include 'header.php'; 

include('Admin/conexao.php'); // A partir da raiz, Admin/conexao.php

$mensagem = '';
$tipo_mensagem = '';

if(isset($_POST['cadastrar'])){
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha_texto_puro = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if ($senha_texto_puro !== $confirmar_senha) {
        $mensagem = "As senhas não coincidem!";
        $tipo_mensagem = 'danger';
    } else {
        $senha_hash = password_hash($senha_texto_puro, PASSWORD_DEFAULT);

        // Verificar se o e-mail já existe
        $stmt_check = $conn->prepare("SELECT id FROM usuarios_site WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $mensagem = "Este e-mail já está cadastrado. Tente fazer login ou use outro e-mail.";
            $tipo_mensagem = 'warning';
        } else {
            $stmt = $conn->prepare("INSERT INTO usuarios_site (nome, email, senha) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sss", $nome, $email, $senha_hash);
                if ($stmt->execute()) {
                    $mensagem = "Cadastro realizado com sucesso! Você pode fazer login agora.";
                    $tipo_mensagem = 'success';
                } else {
                    $mensagem = "Erro ao cadastrar: " . $stmt->error;
                    $tipo_mensagem = 'danger';
                }
                $stmt->close();
            } else {
                $mensagem = "Erro interno ao preparar cadastro: " . $conn->error;
                $tipo_mensagem = 'danger';
            }
        }
        $stmt_check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
    <body class="d-flex flex-column min-vh-100">
        <main class="flex-grow-1 d-flex justify-content-center align-items-center py-5">
            <div class="card p-4 shadow-lg" style="max-width: 500px; width: 100%;">
                <h2 class="card-title text-center mb-4 text-primary">Cadastre-se</h2>
                <?php if (!empty($mensagem)): ?>
                    <div class="alert alert-<?= $tipo_mensagem ?> text-center" role="alert">
                        <?= $mensagem ?>
                    </div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo</label>
                        <input class="form-control" type="text" id="nome" name="nome" placeholder="Seu nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input class="form-control" type="email" id="email" name="email" placeholder="Seu email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input class="form-control" type="password" id="senha" name="senha" placeholder="Crie sua senha" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                        <input class="form-control" type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirme sua senha" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit" name="cadastrar">Cadastrar</button>
                </form>
                <p class="mt-3 text-center">Já tem uma conta? <a href="Admin/login.php">Faça Login</a></p>
            </div>
        </main>

        <footer>
            <p> 2025 ONG Ambiental - Todos os direitos reservados - Mundo Verde</p>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>