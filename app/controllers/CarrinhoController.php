<?php
require_once '../core/Controller.php';

class CarrinhoController extends Controller
{
    public function adicionar($id)
    {
        require_once __DIR__ . '/../models/Produto.php';
        $produtoModel = new Produto();
        $produto = $produtoModel->buscarPorId($id);

        if (!$produto) {
            header('Location: /produto');
            exit;
        }

        $variacoes = $produtoModel->getVariacoes($id);

        $quantidade = isset($_POST['quantidade']) && intval($_POST['quantidade']) > 0 ? intval($_POST['quantidade']) : 1;

        $variacaoEscolhida = isset($_POST['variacao_escolhida'][$id]) ? $_POST['variacao_escolhida'][$id] : null;

        if (!empty($variacoes) && empty($variacaoEscolhida)) {
            $_SESSION['erro'] = 'Selecione ao menos uma variação.';
            header("Location: /produto");
            exit;
        }

        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }

        $chave = $id;
        if (!empty($variacaoEscolhida)) {
            $chave .= '-' . $variacaoEscolhida;
        }

        if (isset($_SESSION['carrinho'][$chave])) {
            $_SESSION['carrinho'][$chave]['quantidade'] += $quantidade;
        } else {

            $variacoesFormatadas = [];
            if (!empty($variacaoEscolhida)) {
                foreach ($variacoes as $var) {
                    if ((string)$var['id'] === (string)$variacaoEscolhida) {
                        $variacoesFormatadas[] = [
                            'id' => $var['id'],
                            'nome_variacao' => $var['nome']
                        ];
                        break;
                    }
                }
            }

            $_SESSION['carrinho'][$chave] = [
                'id'         => $produto['id'],
                'nome'       => $produto['nome'],
                'preco'      => $produto['preco'],
                'quantidade' => $quantidade,
                'variacoes'  => $variacoesFormatadas
            ];
        }

        header('Location: /carrinho/ver');
        exit;
    }

    public function ver()
    {
        $itens = isset($_SESSION['carrinho']) ? $_SESSION['carrinho'] : [];
        $subtotal = 0;

        foreach ($itens as $item) {
            $subtotal += $item['preco'] * $item['quantidade'];
        }

        $frete = $subtotal > 200 ? 0 : 20;

        $desconto_aplicado = 0;
        $cupom_aplicado = isset($_SESSION['cupom_aplicado']) ? $_SESSION['cupom_aplicado'] : null;

        if ($cupom_aplicado) {
            $desconto_aplicado = ($subtotal * $cupom_aplicado['desconto']) / 100;
        }

        $total = $subtotal + $frete - $desconto_aplicado;

        include __DIR__ . '/../views/carrinho/ver.php';
    }

    public function remover($id)
    {
        error_log("ID recebido: $id");
        error_log("Carrinho: " . print_r($_SESSION['carrinho'], true));
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['erro'] = 'Carrinho vazio.';
            header('Location: /carrinho/ver');
            exit;
        }

        $chave_encontrada = null;
        foreach (array_keys($_SESSION['carrinho']) as $chave) {
            if ($chave === $id || strpos($chave, $id . '-') === 0) {
                $chave_encontrada = $chave;
                break;
            }
        }

        if ($chave_encontrada && isset($_SESSION['carrinho'][$chave_encontrada])) {
            unset($_SESSION['carrinho'][$chave_encontrada]);
            $_SESSION['mensagem'] = 'Item removido do carrinho com sucesso.';
        } else {
            $_SESSION['erro'] = 'Item não encontrado no carrinho.';
        }

        header('Location: /carrinho/ver');
        exit;
    }

    public function limpar()
    {
        unset($_SESSION['carrinho']);
        unset($_SESSION['cupom_aplicado']);
        header('Location: /carrinho/ver');
        exit;
    }

    public function aplicarCupom()
    {

        $getDatabaseConnection = function (): PDO {
            $host     = 'localhost';
            $dbname   = 'mini_erp';
            $username = 'root';
            $password = 'Mysql@2024';

            try {
                $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $pdo;
            } catch (PDOException $e) {
                die("Erro na conexão com o banco de dados: " . $e->getMessage());
            }
        };

        require_once __DIR__ . '/../models/Cupom.php';
        $pdo = $getDatabaseConnection();
        $cupomModel = new Cupom($pdo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['cupom'])) {
            $codigo = trim($_POST['cupom']);
            $cupom = $cupomModel->buscarPorCodigo($codigo);

            if (!$cupom) {
                $_SESSION['erro'] = 'Cupom inválido.';
            } elseif (!$cupom['ativo']) {
                $_SESSION['erro'] = 'Cupom inativo.';
            } elseif (strtotime($cupom['validade']) < time()) {
                $_SESSION['erro'] = 'Cupom expirado.';
            } else {
                $subtotal = 0;
                $itens = isset($_SESSION['carrinho']) ? $_SESSION['carrinho'] : [];
                foreach ($itens as $item) {
                    $subtotal += $item['preco'] * $item['quantidade'];
                }

                if ($subtotal < $cupom['valor_minimo']) {
                    $_SESSION['erro'] = 'O subtotal do carrinho é inferior ao valor mínimo do cupom (R$ ' . number_format($cupom['valor_minimo'], 2, ',', '.') . ').';
                } else {
                    $_SESSION['cupom_aplicado'] = [
                        'codigo'   => $cupom['codigo'],
                        'desconto' => $cupom['desconto']
                    ];
                    unset($_SESSION['erro']);
                }
            }
        } else {
            $_SESSION['erro'] = 'Digite um código de cupom.';
        }

        header('Location: /carrinho/ver');
        exit;
    }

    public function sucesso()
    {
        $this->view('carrinho/sucesso');
    }

    public function removerCupom()
    {
        unset($_SESSION['cupom_aplicado']);
        header('Location: /carrinho/ver');
        exit;
    }
}