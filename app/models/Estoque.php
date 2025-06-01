<?php

class Estoque {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function atualizarQuantidade($variacaoId, $quantidade) {
        $stmt = $this->pdo->prepare("UPDATE estoque SET quantidade = ? WHERE variacao_id = ?");
        $stmt->execute([$quantidade, $variacaoId]);
    }

    public function getQuantidadePorVariacao($variacaoId) {
        $stmt = $this->pdo->prepare("SELECT quantidade FROM estoque WHERE variacao_id = ?");
        $stmt->execute([$variacaoId]);
        return $stmt->fetchColumn();
    }
}
