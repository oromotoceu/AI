<?php
// Inclui o header.php que já inicia a sessão
include '../header.php'; 

$inactive = 120; // 2 minutos

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $inactive)) {
    session_unset();     // limpa variáveis
    session_destroy();   // destrói sessão
    header("Location: login.php?msg=Sessão expirada. Faça login novamente.");
    exit();
}

$_SESSION['LAST_ACTIVITY'] = time(); // atualiza último tempo de atividade

if(!isset($_SESSION['logado'])){header("Location: login.php?msg=Acesso restrito, faça login.");exit();}
include('conexao.php'); 

// Exclusão de Usuário do Site
if(isset($_GET['del'])){
    $id = $_GET['del'];
    $stmt = $conn->prepare("DELETE FROM usuarios_site WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: gerenciar_usuarios.php");
        exit();
    } else {
        die("Erro ao preparar a exclusão: " . $conn->error);
    }
}

// Consulta de Usuários do Site
$usuarios_site = $conn->query("SELECT id, nome, email, data_cadastro FROM usuarios_site ORDER BY id DESC"); 
?>

<!DOCTYPE html>
<html lang="pt-br">
    <body>
        <main class="container my-5 p-4 bg-white rounded shadow-sm">
            <h2 class="text-center mb-4">Gerenciar Usuários do Site</h2>
            
            <h3 class="mt-4 mb-3">Lista de Usuários Cadastrados</h3>
            <ul class="list-group">
                <?php if ($usuarios_site->num_rows > 0): ?>
                    <?php while($u = $usuarios_site->fetch_assoc()): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= htmlspecialchars($u['nome']) ?></strong> (Email: <?= htmlspecialchars($u['email']) ?>)
                                <br><small class="text-muted">Cadastrado em: <?= htmlspecialchars($u['data_cadastro']) ?></small>
                            </div>
                            <a class="btn btn-sm btn-danger" href="?del=<?= $u['id'] ?>">Excluir</a>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="alert alert-info text-center" role="alert">
                        Nenhum usuário cadastrado no site ainda.
                    </div>
                <?php endif; ?>
            </ul>
            <a class="btn btn-secondary mt-3 d-block w-25 mx-auto" href="dashboard.php">Voltar ao Painel</a>
        </main>

        <footer>
            <p> 2025 ONG Ambiental - Todos os direitos reservados - Mundo Verde</p>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>