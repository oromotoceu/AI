<?php
// Inclui o header.php que já inicia a sessão e o <head>/<header>
include 'header.php'; 

// Lógica de inatividade da sessão (se não estiver logado, não há 'LAST_ACTIVITY' para verificar)
if (isset($_SESSION['logado']) && isset($_SESSION['LAST_ACTIVITY'])) { // Verifica login ADMIN
    $inatividade = 120; // 2 minutos
    if (time() - $_SESSION['LAST_ACTIVITY'] > $inatividade) {
        session_unset(); 
        session_destroy();  
    } else {
        $_SESSION['LAST_ACTIVITY'] = time(); 
    }
}
// Lógica de inatividade para USUÁRIO DO SITE
if (isset($_SESSION['usuario_logado_id']) && isset($_SESSION['LAST_ACTIVITY'])) {
    $inatividade = 120; // 2 minutos
    if (time() - $_SESSION['LAST_ACTIVITY'] > $inatividade) {
        session_unset(); 
        session_destroy();  
    } else {
        $_SESSION['LAST_ACTIVITY'] = time(); 
    }
}

include('Admin/conexao.php'); // A partir da raiz, Admin/conexao.php

// Consulta para exibir os produtos na página pública
$produtos_publicos = $conn->query("SELECT id, nome, descricao, imagem_url, preco_unitario FROM produtos ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="pt-BR">

    <body>
        <main class="container my-5 p-4 bg-white rounded shadow-sm">
            <h2 class="text-center mb-4">Nossos Produtos</h2>
            <p class="text-center text-muted mb-4">Conheça os produtos e iniciativas da ONG Mundo Verde para a preservação ambiental.</p>

            <?php if ($produtos_publicos->num_rows > 0): ?>
                <?php while($p = $produtos_publicos->fetch_assoc()): ?>
                    <div class="content-section-block d-flex flex-column flex-md-row align-items-center mb-4">
                        <div class="text-block text-center text-md-start me-md-4 mb-3 mb-md-0">
                            <h3><?= htmlspecialchars($p['nome']) ?></h3>
                            <p><?= htmlspecialchars($p['descricao']) ?></p>
                            <p><strong>Preço:</strong> R$ <?= number_format($p['preco_unitario'], 2, ',', '.') ?></p>
                            
                            <?php if (isset($_SESSION['usuario_logado_id'])): // Mostra botão se o usuário do site estiver logado ?>
                                <form action="addcarrinho.php" method="post" class="mt-3">
                                    <input type="hidden" name="produto_id" value="<?= $p['id'] ?>">
                                    <div class="input-group" style="max-width: 150px; margin: 0 auto;">
                                        <input type="number" name="quantidade" class="form-control" value="1" min="1" aria-label="Quantidade" required>
                                        <button class="btn btn-primary" type="submit">Adicionar</button>
                                    </div>
                                </form>
                            <?php else: // Mensagem para fazer login/cadastrar ?>
                                <div class="alert alert-info mt-3" role="alert">
                                    <small>Para adicionar ao carrinho, <a href="login_usuario.php">faça login</a> ou <a href="cadastro.php">cadastre-se</a>.</small>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($p['imagem_url'])): ?>
                            <img src="<?= htmlspecialchars($p['imagem_url']) ?>" alt="Imagem do Produto: <?= htmlspecialchars($p['nome']) ?>" class="img-fluid rounded shadow-sm"> 
                        <?php else: ?>
                            <img src="img/fototeste.jpeg" alt="Imagem padrão do produto" class="img-fluid rounded shadow-sm"> 
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info text-center" role="alert">
                    Nenhum produto disponível no momento.
                </div>
            <?php endif; ?>
        </main>

        <footer>
            <p> 2025 ONG Ambiental - Todos os direitos reservados - Mundo Verde</p>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>