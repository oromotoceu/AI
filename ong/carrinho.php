<?php
include 'header.php'; 

include('Admin/conexao.php'); // A partir da raiz, Admin/conexao.php

$mensagem_carrinho = '';
$tipo_mensagem_carrinho = '';

$itens_carrinho = [];
$total_carrinho = 0;

if (isset($_SESSION['usuario_logado_id'])) {
    $usuario_id = $_SESSION['usuario_logado_id'];

    // Obter o carrinho do usuário
    $stmt_carrinho_id = $conn->prepare("SELECT id FROM carrinho WHERE usuario_id = ?");
    if (!$stmt_carrinho_id) {
        die("Erro ao preparar consulta de carrinho: " . $conn->error);
    }
    $stmt_carrinho_id->bind_param("i", $usuario_id);
    $stmt_carrinho_id->execute();
    $result_carrinho_id = $stmt_carrinho_id->get_result();
    $carrinho_info = $result_carrinho_id->fetch_assoc();
    $carrinho_id = $carrinho_info['id'] ?? null;
    $stmt_carrinho_id->close();

    if ($carrinho_id) {
        // Obter itens do carrinho
        $stmt_itens = $conn->prepare("SELECT ic.id as item_id, ic.quantidade, ic.preco_unitario, p.nome, p.imagem_url 
                                    FROM itens_carrinho ic 
                                    JOIN produtos p ON ic.produto_id = p.id 
                                    WHERE ic.carrinho_id = ?");
        if (!$stmt_itens) {
            die("Erro ao preparar consulta de itens: " . $conn->error);
        }
        $stmt_itens->bind_param("i", $carrinho_id);
        $stmt_itens->execute();
        $result_itens = $stmt_itens->get_result();

        while ($item = $result_itens->fetch_assoc()) {
            $itens_carrinho[] = $item;
            $total_carrinho += ($item['quantidade'] * $item['preco_unitario']);
        }
        $stmt_itens->close();
    }

} 
// A mensagem para "não logado" será exibida no HTML

// Lógica para remover item do carrinho (básico)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['remove_item_id'])) {
    $item_id_to_remove = filter_var($_GET['remove_item_id'], FILTER_VALIDATE_INT);
    if ($item_id_to_remove !== false && isset($_SESSION['usuario_logado_id'])) { // Apenas remove se logado
        $usuario_id = $_SESSION['usuario_logado_id'];

        // Verificar se o item pertence ao carrinho do usuário logado
        $stmt_check_owner = $conn->prepare("SELECT ic.id FROM itens_carrinho ic JOIN carrinho c ON ic.carrinho_id = c.id WHERE ic.id = ? AND c.usuario_id = ?");
        if ($stmt_check_owner) {
            $stmt_check_owner->bind_param("ii", $item_id_to_remove, $usuario_id);
            $stmt_check_owner->execute();
            if ($stmt_check_owner->get_result()->num_rows > 0) {
                $stmt_delete = $conn->prepare("DELETE FROM itens_carrinho WHERE id = ?");
                if ($stmt_delete) {
                    $stmt_delete->bind_param("i", $item_id_to_remove);
                    $stmt_delete->execute();
                    $stmt_delete->close();
                }
            }
            $stmt_check_owner->close();
        }
    }
    // Redireciona para recarregar a página e remover o parâmetro GET
    header("Location: carrinho.php");
    exit();
}

// Exibir mensagem do addcarrinho.php
if (isset($_GET['msg_cart'])) {
    $mensagem_carrinho = htmlspecialchars($_GET['msg_cart']);
    // Determina o tipo de mensagem (success, danger, info, warning) baseado no conteúdo da msg_cart
    if (strpos($mensagem_carrinho, 'Dados do produto inválidos') !== false || strpos($mensagem_carrinho, 'Produto não encontrado') !== false || strpos($mensagem_carrinho, 'Requisição inválida') !== false) {
        $tipo_mensagem_carrinho = 'danger';
    } else {
        $tipo_mensagem_carrinho = 'success';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
    <body class="d-flex flex-column min-vh-100">
        <main class="flex-grow-1 container my-5 p-4 bg-white rounded shadow-sm">
            <h2 class="text-center mb-4 text-primary">Seu Carrinho de Compras</h2>

            <?php if (!empty($mensagem_carrinho)): ?>
                <div class="alert alert-<?= $tipo_mensagem_carrinho ?> text-center" role="alert">
                    <?= $mensagem_carrinho ?>
                </div>
            <?php endif; ?>

            <?php if (!isset($_SESSION['usuario_logado_id'])): ?>
                 <div class="alert alert-info text-center" role="alert">
                    Você precisa estar logado para adicionar e visualizar seu carrinho. <a href="login_usuario.php">Faça Login</a> ou <a href="cadastro.php">Cadastre-se</a>.
                </div>
            <?php elseif (empty($itens_carrinho)): ?>
                <div class="alert alert-info text-center" role="alert">
                    Seu carrinho está vazio. <a href="produtos.php">Adicione alguns produtos!</a>
                </div>
            <?php else: ?>
                <ul class="list-group mb-4">
                    <?php foreach ($itens_carrinho as $item): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <?php if (!empty($item['imagem_url'])): ?>
                                    <img src="<?= htmlspecialchars($item['imagem_url']) ?>" alt="<?= htmlspecialchars($item['nome']) ?>" style="width: 50px; height: 50px; object-fit: cover; margin-right: 15px; border-radius: .25rem;">
                                <?php endif; ?>
                                <div>
                                    <strong><?= htmlspecialchars($item['nome']) ?></strong> x <?= htmlspecialchars($item['quantidade']) ?>
                                    <br>
                                    <small class="text-muted">R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?> por unidade</small>
                                </div>
                            </div>
                            <div>
                                <span>R$ <?= number_format($item['quantidade'] * $item['preco_unitario'], 2, ',', '.') ?></span>
                                <a href="?remove_item_id=<?= $item['item_id'] ?>" class="btn btn-sm btn-danger ms-3">Remover</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="d-flex justify-content-end mb-4">
                    <h3>Total: R$ <?= number_format($total_carrinho, 2, ',', '.') ?></h3>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="produtos.php" class="btn btn-secondary">Continuar Comprando</a>
                    <button class="btn btn-primary" type="button">Finalizar Compra</button>
                </div>
            <?php endif; ?>
        </main>

        <footer>
            <p> 2025 ONG Ambiental - Todos os direitos reservados - Mundo Verde</p>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>