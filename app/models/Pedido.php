<?php

class Pedido {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM pedidos ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function criar($dados)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO pedidos (cliente_nome, cliente_email, endereco, cep, total, frete, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $dados['cliente_nome'],
            $dados['cliente_email'],
            $dados['endereco'],
            $dados['cep'],
            $dados['total'],
            $dados['frete'],
            'pendente'
        ]);

        return $this->pdo->lastInsertId();
    }

    public function inserirItens($pedidoId, $itens)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO pedido_itens (pedido_id, produto_id, variacao_id, quantidade, preco_unitario)
            VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($itens as $item) {
            $produtoId = $item['id'];
            $quantidade = $item['quantidade'];
            $preco = $item['preco'];
            $variacaoId = !empty($item['variacoes']) && isset($item['variacoes'][0]['id'])
                ? $item['variacoes'][0]['id']
                : null;

            $stmt->execute([
                $pedidoId,
                $produtoId,
                $variacaoId,
                $quantidade,
                $preco
            ]);
        }
    }

    public function remover($id) {
        $stmt = $this->pdo->prepare("DELETE FROM pedidos WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function atualizarStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }
}
