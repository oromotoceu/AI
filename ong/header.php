<?php
// Certifique-se de que a sessão está iniciada para usar $_SESSION['logado']
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Lógica para determinar a página ativa e ajustar links
$current_page_name = basename($_SERVER['PHP_SELF']);
$is_admin_page = (isset($_SERVER['PHP_SELF']) && strpos($_SERVER['PHP_SELF'], '/Admin/') !== false);

// Ajuste para reconhecer qual item da navegação deve estar 'ativo'
$active_item = $current_page_name; 

// A lógica para pages admin precisa ser ajustada para o nome da página principal
if ($is_admin_page) {
    if ($current_page_name == 'gerenciar_produtos.php') {
        $active_item = 'produtos.php'; // Refere-se ao link 'Produtos' da nav
    } elseif ($current_page_name == 'novidades.php') { // Admin Novidades
        $active_item = 'novidade.php'; // Refere-se ao link 'Novidades' da nav
    } elseif ($current_page_name == 'administradores.php') { // Admin Administradores
        $active_item = 'dashboard.php'; // Considera o Painel como ativo quando nessa seção admin
    } elseif ($current_page_name == 'login.php') { // Admin Login
        $active_item = 'Admin_login.php'; // Para diferenciar do login de usuário
    } elseif ($current_page_name == 'gerenciar_usuarios.php') { // Gerenciar Usuários do Site
        $active_item = 'dashboard.php'; // Considera Painel como ativo
    } elseif ($current_page_name == 'dashboard.php') {
        $active_item = 'dashboard.php';
    }
}
// As páginas públicas
if (!$is_admin_page) {
    if ($current_page_name == 'produtos.php') {
        $active_item = 'produtos.php';
    } elseif ($current_page_name == 'novidade.php') {
        $active_item = 'novidade.php';
    } elseif ($current_page_name == 'cadastro.php') {
        $active_item = 'cadastro.php';
    } elseif ($current_page_name == 'carrinho.php') {
        $active_item = 'carrinho.php';
    } elseif ($current_page_name == 'login_usuario.php') { // Login de usuário do site
        $active_item = 'login_usuario.php';
    }
}


// Define o título da página para ser usado no <title>
$page_title = 'Mundo Verde';
if ($current_page_name == 'index.php') $page_title = 'Início - Mundo Verde';
elseif ($current_page_name == 'sobre.php') $page_title = 'Sobre - Mundo Verde';
elseif ($current_page_name == 'produtos.php') $page_title = 'Produtos - Mundo Verde';
elseif ($current_page_name == 'novidade.php') $page_title = 'Novidades - Mundo Verde';
elseif ($current_page_name == 'cadastro.php') $page_title = 'Cadastro - Mundo Verde';
elseif ($current_page_name == 'carrinho.php') $page_title = 'Carrinho - Mundo Verde';
elseif ($current_page_name == 'login_usuario.php') $page_title = 'Login de Usuário - Mundo Verde';
elseif ($is_admin_page && $current_page_name == 'login.php') $page_title = 'Login Admin - Mundo Verde';
elseif ($is_admin_page && $current_page_name == 'dashboard.php') $page_title = 'Painel Admin - Mundo Verde';
elseif ($is_admin_page && $current_page_name == 'gerenciar_produtos.php') $page_title = 'Gerenciar Produtos - Mundo Verde';
elseif ($is_admin_page && $current_page_name == 'novidades.php') $page_title = 'Gerenciar Novidades - Mundo Verde';
elseif ($is_admin_page && $current_page_name == 'administradores.php') $page_title = 'Gerenciar Admins - Mundo Verde';
elseif ($is_admin_page && $current_page_name == 'gerenciar_usuarios.php') $page_title = 'Gerenciar Usuários - Mundo Verde';


?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="<?= $is_admin_page ? '../style.css' : 'style.css'; ?>"> 

</head>
<body>
<header>
    <h1><a href="<?= $is_admin_page ? '../index.php' : 'index.php'; ?>" class="header-link">Mundo Verde</a></h1>
    <nav class="nav">
        <ul>
            <li><a href="<?= $is_admin_page ? '../index.php' : 'index.php'; ?>" class="<?= ($active_item == 'index.php') ? 'active-nav' : ''; ?>">Início</a></li>
            <li><a href="<?= $is_admin_page ? '../sobre.php' : 'sobre.php'; ?>" class="<?= ($active_item == 'sobre.php') ? 'active-nav' : ''; ?>">Sobre</a></li>
            <li><a href="<?= $is_admin_page ? '../produtos.php' : 'produtos.php'; ?>" class="<?= ($active_item == 'produtos.php') ? 'active-nav' : ''; ?>">Produtos</a></li>
            <li><a href="<?= $is_admin_page ? '../novidade.php' : 'novidade.php'; ?>" class="<?= ($active_item == 'novidade.php') ? 'active-nav' : ''; ?>">Novidades</a></li>
            <li><a href="<?= $is_admin_page ? '../cadastro.php' : 'cadastro.php'; ?>" class="<?= ($active_item == 'cadastro.php') ? 'active-nav' : ''; ?>">Cadastre-se</a></li>
            <li><a href="<?= $is_admin_page ? '../carrinho.php' : 'carrinho.php'; ?>" class="<?= ($active_item == 'carrinho.php') ? 'active-nav' : ''; ?>">Carrinho</a></li>

            <?php if (isset($_SESSION['logado'])): /* Logado como ADMIN */ ?>
            <li><a href="<?= $is_admin_page ? 'dashboard.php' : 'Admin/dashboard.php'; ?>" class="<?= ($active_item == 'dashboard.php' || $current_page_name == 'gerenciar_produtos.php' || $current_page_name == 'novidades.php' || $current_page_name == 'administradores.php' || $current_page_name == 'gerenciar_usuarios.php') ? 'active-nav' : ''; ?>">Painel Admin</a></li>
            <li><a href="<?= $is_admin_page ? 'logout.php' : 'Admin/logout.php'; ?>">Sair Admin</a></li>
            <?php elseif (isset($_SESSION['usuario_logado_id'])): /* Logado como USUÁRIO DO SITE */ ?>
            <li><a href="<?= $is_admin_page ? '../logout_usuario.php' : 'logout_usuario.php'; ?>">Sair</a></li>
            <?php else: /* NÃO LOGADO */ ?>
            <li><a href="<?= $is_admin_page ? 'login.php' : 'Admin/login.php'; ?>" class="<?= ($active_item == 'Admin_login.php') ? 'active-nav' : ''; ?>">Login Admin</a></li>
            <li><a href="<?= $is_admin_page ? '../login_usuario.php' : 'login_usuario.php'; ?>" class="<?= ($active_item == 'login_usuario.php') ? 'active-nav' : ''; ?>">Login Usuário</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>