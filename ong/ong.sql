-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Tempo de geração: 13/06/2025 às 16:13
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `ong`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `administradores`
--

CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `administradores`
--

INSERT INTO `administradores` (`id`, `nome`, `usuario`, `senha`) VALUES
(1, 'Administrador Principal', 'admin', '123'),
(2, 'gabriel', 'gabriel', '$2y$10$7gnxCNnSyrFd/1Ni4iTwFutyp9utKa4x1OqE9qt.FL.N2KK5yOpty');

-- --------------------------------------------------------

--
-- Estrutura para tabela `carrinho`
--

CREATE TABLE `carrinho` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `carrinho`
--

INSERT INTO `carrinho` (`id`, `usuario_id`, `data_criacao`) VALUES
(1, 1, '2025-06-13 13:50:44');

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_carrinho`
--

CREATE TABLE `itens_carrinho` (
  `id` int(11) NOT NULL,
  `carrinho_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `novidades`
--

CREATE TABLE `novidades` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `conteudo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `novidades`
--

INSERT INTO `novidades` (`id`, `titulo`, `conteudo`) VALUES
(1, 'Lançamento', 'Lançamento Blusa Mundo Verde'),
(2, 'Lançamento', 'Lançamento Copo Mundo Verde');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `imagem_url` varchar(255) DEFAULT 'placeholder.jpg',
  `preco_unitario` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `imagem_url`, `preco_unitario`) VALUES
(3, 'Blusa Ecológica Mundo Verde', 'R$99,99', 'https://i.imgur.com/uSdVSxm.jpeg', 99.99),
(4, 'Copo Ecológico Mundo Verde', 'R$ 20,00', 'https://i.imgur.com/9aWZZc9.jpeg', 20.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios_site`
--

CREATE TABLE `usuarios_site` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios_site`
--

INSERT INTO `usuarios_site` (`id`, `nome`, `email`, `senha`, `data_cadastro`) VALUES
(1, 'gabriel', 'gabriel@gmail.com', '$2y$10$ZIJ63SHJElOeqn7Bc2ziluxOFrN0I0PmQjCUhE1vyxJfTnIhXmY4C', '2025-06-13 13:45:44');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Índices de tabela `carrinho`
--
ALTER TABLE `carrinho`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_carrinho_usuario` (`usuario_id`);

--
-- Índices de tabela `itens_carrinho`
--
ALTER TABLE `itens_carrinho`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_itens_carrinho_carrinho` (`carrinho_id`),
  ADD KEY `fk_itens_carrinho_produto` (`produto_id`);

--
-- Índices de tabela `novidades`
--
ALTER TABLE `novidades`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios_site`
--
ALTER TABLE `usuarios_site`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `carrinho`
--
ALTER TABLE `carrinho`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `itens_carrinho`
--
ALTER TABLE `itens_carrinho`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `novidades`
--
ALTER TABLE `novidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `usuarios_site`
--
ALTER TABLE `usuarios_site`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `carrinho`
--
ALTER TABLE `carrinho`
  ADD CONSTRAINT `fk_carrinho_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios_site` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `itens_carrinho`
--
ALTER TABLE `itens_carrinho`
  ADD CONSTRAINT `fk_itens_carrinho_carrinho` FOREIGN KEY (`carrinho_id`) REFERENCES `carrinho` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_itens_carrinho_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
