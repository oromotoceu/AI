<?php
// Inclui o header.php que já inicia a sessão e o <head>/<header>
include 'header.php'; 

// Lógica de inatividade da sessão (se não estiver logado, não há 'LAST_ACTIVITY' para verificar)
if (isset($_SESSION['logado']) && isset($_SESSION['LAST_ACTIVITY'])) {
    $inatividade = 120; // 2 minutos
    if (time() - $_SESSION['LAST_ACTIVITY'] > $inatividade) {
        session_unset(); 
        session_destroy();  
    } else {
        $_SESSION['LAST_ACTIVITY'] = time(); 
    }
}

include('Admin/conexao.php'); // A partir da raiz, Admin/conexao.php
// Consulta para exibir as novidades do banco
$novidades_publicas = $conn->query("SELECT id, titulo, conteudo FROM novidades ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="pt-BR">

    <body>
        <main class="container my-5 p-4 bg-white rounded shadow-sm">
            <h2 class="text-center mb-4">Nossas Novidades</h2>
            <p class="text-center text-muted mb-4">Fique por dentro das últimas ações, eventos e campanhas da nossa ONG.</p>

            <div class="list-group">
                <?php if ($novidades_publicas->num_rows > 0): ?>
                    <?php while($n = $novidades_publicas->fetch_assoc()): ?>
                        <a href="#" class="list-group-item list-group-item-action">
                            <h5 class="mb-1 text-primary"><?= htmlspecialchars($n['titulo']) ?></h5>
                            <small class="text-muted">Publicado em <?php // Adicionar data da novidade se houver no DB ?></small>
                            <p class="mb-1"><?= htmlspecialchars($n['conteudo']) ?></p>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="alert alert-info text-center" role="alert">
                        Nenhuma novidade disponível no momento.
                    </div>
                <?php endif; ?>
            </div>
        </main>

        <footer>
            <p> 2025 ONG Ambiental - Todos os direitos reservados - Mundo Verde</p>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>