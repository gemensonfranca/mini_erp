<?php

class Cupom
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listarTodos(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM cupons");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salvar(array $dados): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO cupons (codigo, desconto, validade, valor_minimo, ativo)
            VALUES (?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $dados['codigo'],
            $dados['desconto'],
            $dados['validade'],
            $dados['valor_minimo'],
            $dados['ativo']
        ]);
    }

    public function buscarPorCodigo(string $codigo): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM cupons WHERE codigo = :codigo LIMIT 1");
        $stmt->bindValue(':codigo', $codigo);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ?: null;
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM cupons WHERE id = ?");
        $stmt->execute([$id]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ?: null;
    }

    public function atualizar(int $id, array $dados): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE cupons
            SET codigo = ?, desconto = ?, validade = ?, valor_minimo = ?, ativo = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $dados['codigo'],
            $dados['desconto'],
            $dados['validade'],
            $dados['valor_minimo'],
            $dados['ativo'],
            $id
        ]);
    }

    public function excluir(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM cupons WHERE id = ?");
        return $stmt->execute([$id]);
    }
}