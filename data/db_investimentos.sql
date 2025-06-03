-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3312
-- Tempo de geração: 03/06/2025 às 00:07
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `db_investimentos`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `investimentos`
--

CREATE TABLE `investimentos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1,
  `data_investimento` date NOT NULL,
  `observacoes` text DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `investimentos`
--

INSERT INTO `investimentos` (`id`, `user_id`, `titulo`, `tipo`, `valor`, `quantidade`, `data_investimento`, `observacoes`, `data_criacao`) VALUES
(1, 0, 'BBAS3', 'Ações', 335.00, 1, '2025-04-23', 'N/A', '2025-05-30 20:03:41'),
(2, 0, 'MXRF11', 'FIIs', 50.00, 5, '2025-05-30', 'n/a', '2025-05-30 22:24:00'),
(4, 1, 'BBAS3F', 'Ações', 375.00, 14, '2025-05-30', 'N/A', '2025-05-30 22:58:10'),
(5, 1, 'CPTS11', 'FIIs', 250.00, 15, '2025-05-30', 'n/a', '2025-05-30 22:59:19'),
(6, 1, 'Selic 2032', 'Tesouro Direto', 750.00, 1, '2025-05-30', 'N/A', '2025-05-30 23:00:18'),
(7, 2, 'MXRF11', 'FIIs', 760.00, 55, '2025-05-30', 'N/A', '2025-05-30 23:01:41'),
(8, 2, 'IPCA 2029 + 7%', 'LCI/LCA', 1000.00, 1, '2025-05-30', 'N/A', '2025-05-30 23:02:43'),
(9, 2, 'KEPL3', 'Ações', 465.00, 45, '2025-05-30', 'N/A', '2025-05-30 23:03:27');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'dheymes', '$2y$10$VddLy8lQGDTtF1Lqy.rAiefZ1F3YKqi.LgUKj5RzNb1Ope5UM7H.G', '2025-05-30 20:25:24'),
(2, 'damiao', '$2y$10$86SZe/SqzwzLZQKKV7Tda.jr2F5ZX1vrz1Ud6.4GxZVpnrsfg4HpG', '2025-05-30 22:29:19'),
(3, 'tiaodafal', '$2y$10$xQXjsfcpp43fRCqeGfm5J.B7CCnB1A42Mz1OIiju3rLnoyMIHCFhe', '2025-05-31 13:03:01');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `investimentos`
--
ALTER TABLE `investimentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `investimentos`
--
ALTER TABLE `investimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
