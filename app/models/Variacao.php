<?php

class Variacao {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function salvar($dados) {

        $stmt = $this->pdo->prepare("SELECT id FROM variacoes WHERE produto_id = ? AND nome = ?");
        $stmt->execute([$dados['produto_id'], $dados['nome']]);
        $variacaoId = $stmt->fetchColumn();

        if ($variacaoId) {
            $stmt = $this->pdo->prepare("UPDATE estoque SET quantidade = ? WHERE variacao_id = ?");
            $stmt->execute([$dados['quantidade'], $variacaoId]);
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO variacoes (produto_id, nome) VALUES (?, ?)");
            $stmt->execute([$dados['produto_id'], $dados['nome']]);
            $variacaoId = $this->pdo->lastInsertId();

            $stmt = $this->pdo->prepare("INSERT INTO estoque (variacao_id, quantidade) VALUES (?, ?)");
            $stmt->execute([$variacaoId, $dados['quantidade']]);
        }
    }

    public function getByProduto($produtoId) {
        $stmt = $this->pdo->prepare("SELECT v.id, v.nome, e.quantidade FROM variacoes v LEFT JOIN estoque e ON v.id = e.variacao_id WHERE v.produto_id = ?");
        $stmt->execute([$produtoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
