-- ============================================================================
-- BANCO DE DADOS: DURK · Streetwear Underground
-- Para importar no phpMyAdmin: crie o banco "durk" e importe este arquivo
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `durk` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `durk`;

-- ----------------------------------------------------------------------------
-- USUÁRIOS
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `senha_hash` VARCHAR(255) NOT NULL,
  `telefone` VARCHAR(20) DEFAULT NULL,
  `tipo_usuario` ENUM('usuario','admin') NOT NULL DEFAULT 'usuario',
  `data_cadastro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- CATEGORIAS
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(80) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- MARCAS
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `marcas`;
CREATE TABLE `marcas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(80) NOT NULL,
  `descricao` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- TAMANHOS
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tamanhos`;
CREATE TABLE `tamanhos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `descricao` VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- PRODUTOS
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `produtos`;
CREATE TABLE `produtos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(180) NOT NULL,
  `descricao` TEXT,
  `preco` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `estoque` INT NOT NULL DEFAULT 0,
  `categoria_id` INT,
  `marca_id` INT,
  `genero` ENUM('masculino','feminino','unissex') NOT NULL DEFAULT 'unissex',
  `slug` VARCHAR(200),
  `data_cadastro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`categoria_id`) REFERENCES `categorias`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`marca_id`) REFERENCES `marcas`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- PRODUTO X TAMANHOS
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `produto_tamanhos`;
CREATE TABLE `produto_tamanhos` (
  `produto_id` INT NOT NULL,
  `tamanho_id` INT NOT NULL,
  PRIMARY KEY (`produto_id`, `tamanho_id`),
  FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tamanho_id`) REFERENCES `tamanhos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- IMAGENS DOS PRODUTOS
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `imagens_produto`;
CREATE TABLE `imagens_produto` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `produto_id` INT NOT NULL,
  `url_imagem` VARCHAR(255) NOT NULL,
  `ordem` INT DEFAULT 0,
  FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- FAVORITOS
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `favoritos`;
CREATE TABLE `favoritos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `produto_id` INT NOT NULL,
  `data_criacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_user_produto` (`user_id`, `produto_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- CARRINHOS
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `carrinhos`;
CREATE TABLE `carrinhos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `data_criacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- ITENS DO CARRINHO
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `itens_carrinho`;
CREATE TABLE `itens_carrinho` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `carrinho_id` INT NOT NULL,
  `produto_id` INT NOT NULL,
  `tamanho_id` INT DEFAULT NULL,
  `quantidade` INT NOT NULL DEFAULT 1,
  FOREIGN KEY (`carrinho_id`) REFERENCES `carrinhos`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tamanho_id`) REFERENCES `tamanhos`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- ENDEREÇOS
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `enderecos`;
CREATE TABLE `enderecos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `rua` VARCHAR(180) NOT NULL,
  `numero` VARCHAR(20) NOT NULL,
  `complemento` VARCHAR(100) DEFAULT NULL,
  `bairro` VARCHAR(100) NOT NULL,
  `cidade` VARCHAR(100) NOT NULL,
  `estado` VARCHAR(50) NOT NULL,
  `cep` VARCHAR(20) NOT NULL,
  `pais` VARCHAR(50) NOT NULL DEFAULT 'Brasil',
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- PEDIDOS
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `pedidos`;
CREATE TABLE `pedidos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `endereco_id` INT DEFAULT NULL,
  `status` ENUM('Pendente','Pago','Enviado','Entregue','Cancelado') NOT NULL DEFAULT 'Pendente',
  `data_pedido` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `valor_total` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `metodo_pagamento` VARCHAR(30) DEFAULT 'pix',
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`endereco_id`) REFERENCES `enderecos`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- ITENS DO PEDIDO
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `itens_pedido`;
CREATE TABLE `itens_pedido` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pedido_id` INT NOT NULL,
  `produto_id` INT NOT NULL,
  `tamanho_id` INT DEFAULT NULL,
  `quantidade` INT NOT NULL DEFAULT 1,
  `preco_unitario` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`pedido_id`) REFERENCES `pedidos`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tamanho_id`) REFERENCES `tamanhos`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- DADOS INICIAIS (seed) — pra você já abrir a loja com conteúdo
-- ============================================================================

-- Usuário ADMIN padrão
-- Email: admin@durk.com   ·   Senha: admin123
INSERT INTO `users` (`nome`, `email`, `senha_hash`, `telefone`, `tipo_usuario`) VALUES
('Admin Durk', 'admin@durk.com', '$2y$10$ZzZx5g8x.A50p9YzcL5Y4eL5HvKQ4lG7p1nA0d5wEmJv5DDsxR8eO', '(11) 99999-9999', 'admin'),
('Cliente Teste', 'cliente@durk.com', '$2y$10$LXqJ1lH9N5fzD3F7Q8sR7e3KuOQqW3W1nGgFqJK7y3JzU6Y6Yvqea', '(11) 98888-8888', 'usuario');

-- Categorias
INSERT INTO `categorias` (`nome`, `slug`) VALUES
('Camisetas', 'camisetas'),
('Moletons', 'moletons'),
('Calças', 'calcas'),
('Jaquetas', 'jaquetas'),
('Acessórios', 'acessorios'),
('Bonés', 'bones');

-- Marcas
INSERT INTO `marcas` (`nome`, `descricao`) VALUES
('Durk', 'A marca oficial do streetwear underground.'),
('Syna', 'Colab autoral com a Syna World.'),
('Corteiz', 'Drill, drip e atitude da rua.'),
('Trapstar', 'Cultura de rua de Londres.'),
('Stüssy', 'Pioneira do streetwear.');

-- Tamanhos
INSERT INTO `tamanhos` (`descricao`) VALUES
('PP'), ('P'), ('M'), ('G'), ('GG'), ('XGG'), ('38'), ('40'), ('42'), ('44');

-- Produtos de exemplo (usando as imagens que já existem em views/imagens/)
INSERT INTO `produtos` (`nome`, `descricao`, `preco`, `estoque`, `categoria_id`, `marca_id`, `genero`, `slug`) VALUES
('Jaqueta Tech Fleece Dk X Syna', 'Moletom oversized em tech fleece, colab Durk x Syna. Atitude do asfalto na pele.', 299.90, 25, 2, 2, 'unissex', 'jaqueta-tech-fleece-dk-syna'),
('Camiseta Oversized Durk', 'Camiseta oversized de algodão pesado com estampa exclusiva Durk.', 89.90, 50, 1, 1, 'unissex', 'camiseta-oversized-durk'),
('Cinto Masculino Durk', 'Cinto de couro sintético com fivela autoral Durk.', 69.90, 40, 5, 1, 'masculino', 'cinto-masculino-durk');

-- Vincular tamanhos aos produtos
INSERT INTO `produto_tamanhos` (`produto_id`, `tamanho_id`) VALUES
(1, 2), (1, 3), (1, 4), (1, 5),
(2, 2), (2, 3), (2, 4), (2, 5), (2, 6),
(3, 3), (3, 4), (3, 5);

-- Imagens dos produtos (use as que já estão na pasta views/imagens/)
INSERT INTO `imagens_produto` (`produto_id`, `url_imagem`, `ordem`) VALUES
(1, 'views/imagens/techfleece.png', 0),
(2, 'views/imagens/oversizeddk.png', 0),
(3, 'views/imagens/cintodk.png', 0);

SET FOREIGN_KEY_CHECKS = 1;
