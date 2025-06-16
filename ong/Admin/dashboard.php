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
?>

<!DOCTYPE html>
<html lang="pt-br">
    <body>
        <main class="container my-5 p-4 bg-white rounded shadow-sm"> 
            <h2 class="text-center mb-4">Painel Administrativo</h2> 
            <div class="row justify-content-center g-3">
                <div class="col-md-4">
                    <a class="btn btn-primary w-100 py-3" href="gerenciar_produtos.php">Gerenciar Produtos</a>
                </div>
                <div class="col-md-4">
                    <a class="btn btn-primary w-100 py-3" href="novidades.php">Gerenciar Novidades</a>
                </div>
                <div class="col-md-4">
                    <a class="btn btn-primary w-100 py-3" href="administradores.php">Gerenciar Administradores</a>
                </div>
            </div>
        </main>

        <footer>
            <p> 2025 ONG Ambiental - Todos os direitos reservados - Mundo Verde</p>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>