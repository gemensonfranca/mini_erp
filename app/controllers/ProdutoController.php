<?php
require_once '../core/Controller.php';

class ProdutoController extends Controller
{
    public function index()
    {
        $produtoModel = $this->model('Produto');
        $produtos = $produtoModel->getAll();

        foreach ($produtos as &$produto) {
            $produto['variacoes'] = $produtoModel->getVariacoes($produto['id']);
            $produto['estoque_total'] = $produtoModel->getEstoqueTotal($produto['id']);
        }
        unset($produto);

        $this->view('produtos/form', ['produtos' => $produtos]);
    }

    public function salvar() {
        $dados = $_POST;

        $produtoModel = $this->model('Produto');
        $produtoModel->salvar($dados);

        header('Location: /produto');
        exit;
    }

    public function editar($id)
    {
        $produtoModel = $this->model('Produto');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $produtoModel->update($id, [
                'nome'  => $_POST['nome'] ?? '',
                'preco' => $_POST['preco'] ?? 0
            ]);

            $produtoModel->atualizarVariacoesEEstoque($id, $_POST['variacoes'] ?? '');

            header('Location: /produto');
            exit;
        }

        $produto = $produtoModel->getById($id);
        $variacoes = $produtoModel->getVariacoes($id);
        $estoqueTotal = $produtoModel->getEstoqueTotal($id);

        $this->view('produtos/form', [
            'produto'   => $produto,
            'variacoes' => $variacoes,
            'estoque'   => ['quantidade' => $estoqueTotal]
        ]);
    }

    public function excluir($id) {
        $produtoModel = $this->model('Produto');
        $produtoModel->excluir($id);

        header('Location: /produto');
        exit;
    }
}
