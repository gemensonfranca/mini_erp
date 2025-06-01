CREATE DATABASE mini_erp;
USE mini_erp;

CREATE TABLE `produtos` (
    `id` int NOT NULL AUTO_INCREMENT,
    `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `preco` decimal(10,2) NOT NULL,
    `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `variacoes` (
    `id` int NOT NULL AUTO_INCREMENT,
    `produto_id` int NOT NULL,
    `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `produto_id` (`produto_id`),
    CONSTRAINT `variacoes_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `estoque` (
    `id` int NOT NULL AUTO_INCREMENT,
    `produto_id` int NOT NULL,
    `variacao_id` int DEFAULT NULL,
    `quantidade` int NOT NULL DEFAULT '0',
    `atualizado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `produto_id` (`produto_id`),
    KEY `variacao_id` (`variacao_id`),
    CONSTRAINT `estoque_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE,
    CONSTRAINT `estoque_ibfk_2` FOREIGN KEY (`variacao_id`) REFERENCES `variacoes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cupons` (
    `id` int NOT NULL AUTO_INCREMENT,
    `codigo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `desconto` decimal(10,2) NOT NULL,
    `validade` date NOT NULL,
    `valor_minimo` decimal(10,2) NOT NULL DEFAULT '0.00',
    `ativo` tinyint(1) NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`),
    UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pedidos` (
    `id` int NOT NULL AUTO_INCREMENT,
    `cliente_nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `cliente_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `endereco` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `cep` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `total` decimal(10,2) NOT NULL,
    `frete` decimal(10,2) NOT NULL,
    `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendente',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pedido_itens` (
    `id` int NOT NULL AUTO_INCREMENT,
    `pedido_id` int NOT NULL,
    `produto_id` int NOT NULL,
    `variacao_id` int DEFAULT NULL,
    `quantidade` int NOT NULL,
    `preco_unitario` decimal(10,2) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `pedido_id` (`pedido_id`),
    CONSTRAINT `pedido_itens_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
