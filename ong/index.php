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
?>

<!DOCTYPE html>
<html lang="pt-BR">

    <body>
        <main>
            <div class="hero-section">
                <img src="img/FundoMundoVerde.png" alt="Natureza exuberante, floresta com árvores verdes."> 

                <div class="hero-text">
                    <h2>Seja Bem-Vindo ao Mundo Verde</h2>
                    <p>#1 em Meio Ambiente</p>
                </div>
            </div>

            <div class="content-section-block">
                <div class="text-block">
                    <h3>Nossa Missão: Proteger e Transformar</h3>
                    <p>
                        A ONG Mundo Verde é uma organização dedicada à preservação ambiental e à promoção da sustentabilidade em nossa comunidade e no planeta. Desde a nossa fundação, atuamos com paixão e compromisso para enfrentar os desafios ambientais mais urgentes do nosso tempo.
                    </p>
                    <p>
                        Acreditamos que cada pequena ação faz a diferença. Por isso, trabalhamos incansavelmente em projetos de reflorestamento, educação ambiental, limpeza de ecossistemas e fomento a práticas sustentáveis. Nosso objetivo é construir um futuro mais verde e consciente para todos.
                    </p>
                    <p class="mt-4 text-center">
                        <a href="sobre.php" class="btn btn-primary">Saiba Mais Sobre Nós</a>
                        <a href="produtos.php" class="btn btn-secondary ms-2">Conheça Nossos Produtos</a>
                    </p>
                </div>
                <img src="img/MundoVerdeFuturo.png" alt="Mão segurando uma pequena planta brotando.">
            </div>
        </main>

        <footer>
            <p> 2025 ONG Ambiental - Todos os direitos reservados - Mundo Verde</p>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>