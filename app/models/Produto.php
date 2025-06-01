<?php
class Produto extends Model {

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM produtos");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getVariacoes($produto_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM variacoes WHERE produto_id = ?");
        $stmt->execute([$produto_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEstoqueTotal($produto_id) {
        $stmt = $this->pdo->prepare("
            SELECT SUM(e.quantidade) AS total
            FROM estoque e
            INNER JOIN variacoes v ON v.id = e.variacao_id
            WHERE v.produto_id = ?
        ");
        $stmt->execute([$produto_id]);
        return (int) ($stmt->fetchColumn() ?? 0);
    }

    public function update($id, $dados) {
        $sql = "UPDATE produtos SET nome = :nome, preco = :preco WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nome', $dados['nome']);
        $stmt->bindValue(':preco', $dados['preco']);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function atualizarVariacoesEEstoque($produto_id, $variacoesInput)
    {
        $stmtEstoque = $this->pdo->prepare("DELETE FROM estoque WHERE produto_id = ?");
        $stmtEstoque->execute([$produto_id]);

        $stmtVariacoes = $this->pdo->prepare("DELETE FROM variacoes WHERE produto_id = ?");
        $stmtVariacoes->execute([$produto_id]);

        $variacoes = array_map('trim', explode(',', $variacoesInput));
        
        $stmtVar = $this->pdo->prepare("INSERT INTO variacoes (produto_id, nome) VALUES (?, ?)");
        $stmtEstoque = $this->pdo->prepare("INSERT INTO estoque (produto_id, variacao_id, quantidade) VALUES (?, ?, ?)");

        foreach ($variacoes as $var) {
            if (preg_match('/(.+)\s*\((\d+)\)/', $var, $matches)) {
                $nome = trim($matches[1]);
                $quantidade = (int) $matches[2];

                $stmtVar->execute([$produto_id, $nome]);
                $variacao_id = $this->pdo->lastInsertId();

                $stmtEstoque->execute([$produto_id, $variacao_id, $quantidade]);
            }
        }
    }

    public function salvar($dados) {
        $stmt = $this->pdo->prepare("INSERT INTO produtos (nome, preco) VALUES (?, ?)");
        $stmt->execute([$dados['nome'], $dados['preco']]);
        $produto_id = $this->pdo->lastInsertId();

        if (!empty($dados['variacoes'])) {
            $this->inserirVariacoesEEstoque($produto_id, $dados['variacoes']);
        }

        return $produto_id;
    }

    public function excluir($id) {

        $stmt = $this->pdo->prepare("
            DELETE FROM estoque 
            WHERE variacao_id IN (SELECT id FROM variacoes WHERE produto_id = ?)
        ");
        $stmt->execute([$id]);

        $stmt = $this->pdo->prepare("DELETE FROM variacoes WHERE produto_id = ?");
        $stmt->execute([$id]);

        $stmt = $this->pdo->prepare("DELETE FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function buscarPorId($id)
    {
        $sql = "SELECT * FROM produtos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function inserirVariacoesEEstoque($produto_id, $variacoesInput) {
        $variacoes = array_map('trim', explode(',', $variacoesInput));

        $stmtVar = $this->pdo->prepare("INSERT INTO variacoes (produto_id, nome) VALUES (?, ?)");
        $stmtEstoque = $this->pdo->prepare("INSERT INTO estoque (produto_id, variacao_id, quantidade) VALUES (?, ?, ?)");

        foreach ($variacoes as $var) {
            if (preg_match('/(.+)\s*\((\d+)\)/', $var, $matches)) {
                $nome = trim($matches[1]);
                $quantidade = (int) $matches[2];

                $stmtVar->execute([$produto_id, $nome]);
                $variacao_id = $this->pdo->lastInsertId();

                $stmtEstoque->execute([$produto_id, $variacao_id, $quantidade]);
            }
        }
    }
}
