<?php
// Inclui o header.php que já inicia a sessão e o <head>/<header>
include '../header.php'; // Caminho correto para header.php a partir de Admin/

$inactive = 120; // 2 minutos

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $inactive)) {
    session_unset();     // limpa variáveis
    session_destroy();   // destrói sessão
    header("Location: login.php?msg=Sessão expirada. Faça login novamente.");
    exit();
}

$_SESSION['LAST_ACTIVITY'] = time(); // atualiza último tempo de atividade

if(!isset($_SESSION['logado'])){header("Location: login.php?msg=Acesso restrito, faça login.");exit();}
include('conexao.php'); // conexao.php está na mesma pasta Admin/

// Inserção de Administrador
if(isset($_POST['add'])){
    $nome = $_POST['nome'];
    $usuario = $_POST['usuario']; // Nome de usuário para login
    $senha_texto_puro = $_POST['senha'];
    $senha_hash = password_hash($senha_texto_puro, PASSWORD_DEFAULT); // Hash da senha para segurança

    $stmt = $conn->prepare("INSERT INTO administradores (nome, usuario, senha) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $nome, $usuario, $senha_hash); 
        $stmt->execute();
        $stmt->close();
        header("Location: administradores.php");
        exit();
    } else {
        die("Erro ao preparar a inserção: " . $conn->error);
    }
}

// Exclusão de Administrador
if(isset($_GET['del'])){
    $id = $_GET['del'];
    $stmt = $conn->prepare("DELETE FROM administradores WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: administradores.php");
        exit();
    } else {
        die("Erro ao preparar a exclusão: " . $conn->error);
    }
}

// Consulta de Administradores
$administradores = $conn->query("SELECT id, nome, usuario FROM administradores ORDER BY id DESC"); 
?>

<!DOCTYPE html>
<html lang="pt-br">
    <body>
        <main class="container my-5 p-4 bg-white rounded shadow-sm">
            <h2 class="text-center mb-4">Gerenciar Administradores</h2>
            <form method="post" class="p-4 mb-4 bg-light rounded">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome Completo</label>
                    <input class="form-control" type="text" id="nome" name="nome" placeholder="Nome do Administrador" required>
                </div>
                <div class="mb-3">
                    <label for="usuario" class="form-label">Nome de Usuário (Login)</label>
                    <input class="form-control" type="text" id="usuario" name="usuario" placeholder="Nome de Usuário do Administrador" required>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input class="form-control" type="password" id="senha" name="senha" placeholder="Senha do Administrador" required>
                </div>
                <button class="btn btn-primary" type="submit" name="add">Adicionar Administrador</button>
            </form>

            <h3 class="mt-4 mb-3">Lista de Administradores</h3>
            <ul class="list-group">
                <?php while($a = $administradores->fetch_assoc()): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= htmlspecialchars($a['nome']) ?></strong> (Usuário: <?= htmlspecialchars($a['usuario']) ?>)
                        </div>
                        <a class="btn btn-sm btn-danger" href="?del=<?= $a['id'] ?>">Excluir</a>
                    </li>
                <?php endwhile; ?>
            </ul>
            <a class="btn btn-secondary mt-3 d-block w-25 mx-auto" href="dashboard.php">Voltar ao Painel</a>
        </main>

        <footer>
            <p> 2025 ONG Ambiental - Todos os direitos reservados - Mundo Verde</p>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>