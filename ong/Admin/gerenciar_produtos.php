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

// Inserção
if(isset($_POST['add'])){
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $imagem_url = $_POST['imagem_url']; // Novo campo
    
    $stmt = $conn->prepare("INSERT INTO produtos (nome, descricao, imagem_url) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $nome, $descricao, $imagem_url); 
        $stmt->execute();
        $stmt->close();
        header("Location: gerenciar_produtos.php");
        exit();
    } else {
        die("Erro ao preparar a inserção: " . $conn->error);
    }
}

// Exclusão
if(isset($_GET['del'])){
    $id = $_GET['del'];
    
    $stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: gerenciar_produtos.php");
        exit();
    } else {
        die("Erro ao preparar a exclusão: " . $conn->error);
    }
}

// Consulta
$produtos = $conn->query("SELECT id, nome, descricao, imagem_url FROM produtos ORDER BY id DESC"); 
?>

<!DOCTYPE html>
<html lang="pt-br">
    <body>
        <main class="container my-5 p-4 bg-white rounded shadow-sm">
            <h2 class="text-center mb-4">Gerenciar Produtos</h2>
            <form method="post" class="p-4 mb-4 bg-light rounded">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome do Produto</label>
                    <input class="form-control" type="text" id="nome" name="nome" placeholder="Nome do Produto" required>
                </div>
                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="descricao" name="descricao" placeholder="Descrição Detalhada do Produto" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="imagem_url" class="form-label">URL da Imagem</label>
                    <input class="form-control" type="url" id="imagem_url" name="imagem_url" placeholder="Ex: http://exemplo.com/imagem.jpg">
                </div>
                <button class="btn btn-primary" type="submit" name="add">Adicionar Produto</button>
            </form>

            <h3 class="mt-4 mb-3">Lista de Produtos</h3>
            <ul class="list-group">
                <?php while($p = $produtos->fetch_assoc()): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= htmlspecialchars($p['nome']) ?></strong>: <?= htmlspecialchars($p['descricao']) ?>
                            <?php if (!empty($p['imagem_url'])): ?>
                                <br><img src="<?= htmlspecialchars($p['imagem_url']) ?>" alt="Imagem do Produto" style="max-width: 100px; height: auto; margin-top: 10px; border-radius: 5px;">
                            <?php endif; ?>
                        </div>
                        <a class="btn btn-sm btn-danger" href="?del=<?= $p['id'] ?>">Excluir</a>
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