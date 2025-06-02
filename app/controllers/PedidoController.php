<?php
require_once '../core/Controller.php';

class PedidoController extends Controller {

    public function listar() {
        $pedidoModel = $this->model('Pedido');
        $pedidos = $pedidoModel->getAll();
        $this->view('pedidos/lista', ['pedidos' => $pedidos]);
    }

    public function criar() {
        $pedidoModel = $this->model('Pedido');
        $pedidoModel->criar($_POST);
        header('Location: /pedido');
    }

    public function webhook() {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!isset($data['id']) || !isset($data['status'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos']);
            exit;
        }

        $pedidoModel = $this->model('Pedido');

        if ($data['status'] === 'cancelado') {
            $pedidoModel->remover($data['id']);
        } else {
            $pedidoModel->atualizarStatus($data['id'], $data['status']);
        }

        http_response_code(200);
        echo json_encode(['success' => true]);
    }

    public function finalizar()
    {
        if (empty($_SESSION['usuario'])) {
            header('Location: /usuario/cadastrar');
            exit;
        }

        if (empty($_SESSION['carrinho'])) {
            echo "Carrinho vazio.";
            exit;
        }

        $usuario = $_SESSION['usuario'];
        $itens = $_SESSION['carrinho'];
        $cupom_aplicado = isset($_SESSION['cupom_aplicado']) ? $_SESSION['cupom_aplicado'] : null;

        $subtotal = 0;
        foreach ($itens as $item) {
            $subtotal += $item['preco'] * $item['quantidade'];
        }

        $frete = $subtotal > 200 ? 0 : 20;
        $desconto = 0;

        if ($cupom_aplicado) {
            $desconto = ($subtotal * $cupom_aplicado['desconto']) / 100;
        }

        $total = $subtotal + $frete - $desconto;

        $pedidoModel = $this->model('Pedido');

        foreach ($itens as $item) {
            $produto_id = $item['id'];
            $variacao_id = !empty($item['variacoes']) ? $item['variacoes'][0]['id'] : null;
            $quantidade = $item['quantidade'];

            $stmt = $this->pdo->prepare("
                SELECT quantidade FROM estoque 
                WHERE produto_id = ? AND (variacao_id = ? OR variacao_id IS NULL)
            ");
            $stmt->execute([$produto_id, $variacao_id]);
            $estoque = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$estoque || $estoque['quantidade'] < $quantidade) {
                $_SESSION['erro'] = "Estoque insuficiente para o produto {$item['nome']}" . 
                    (!empty($item['variacoes']) ? " ({$item['variacoes'][0]['nome_variacao']})" : '');
                header('Location: /carrinho/ver');
                exit;
            }
        }

        $pedidoId = $pedidoModel->criar([
            'cliente_nome'  => $usuario['nome'],
            'cliente_email' => $usuario['email'],
            'endereco'      => $usuario['endereco'],
            'cep'           => $usuario['cep'],
            'total'         => $total,
            'frete'         => $frete,
            'cupom_codigo'  => $cupom_aplicado ? $cupom_aplicado['codigo'] : null,
            'desconto'      => $desconto
        ]);

        $pedidoModel->inserirItens($pedidoId, $itens);

        foreach ($itens as $item) {
            $produto_id = $item['id'];
            $variacao_id = !empty($item['variacoes']) ? $item['variacoes'][0]['id'] : null;
            $quantidade = $item['quantidade'];

            $stmt = $this->pdo->prepare("
                UPDATE estoque 
                SET quantidade = quantidade - ? 
                WHERE produto_id = ? AND (variacao_id = ? OR variacao_id IS NULL)
            ");
            $stmt->execute([$quantidade, $produto_id, $variacao_id]);
        }

        $mensagem  = "Olá, {$usuario['nome']}!\n\n";
        $mensagem .= "Seu pedido #$pedidoId foi confirmado.\n\n";
        $mensagem .= "Detalhes do seu pedido:\n";

        foreach ($itens as $item) {
            $linha = "- {$item['nome']} x{$item['quantidade']} (R$ " . number_format($item['preco'], 2, ',', '.') . ")";
            if (!empty($item['variacoes'])) {
                $nomes = array_column($item['variacoes'], 'nome_variacao');
                $linha .= " [" . implode(', ', $nomes) . "]";
            }
            $mensagem .= $linha . "\n";
        }

        $mensagem .= "\nSubtotal: R$ " . number_format($subtotal, 2, ',', '.');
        if ($cupom_aplicado) {
            $mensagem .= "\nCupom ({$cupom_aplicado['codigo']}): - R$ " . number_format($desconto, 2, ',', '.');
        }
        $mensagem .= "\nFrete: " . ($frete == 0 ? 'Grátis' : 'R$ ' . number_format($frete, 2, ',', '.'));
        $mensagem .= "\nTotal: R$ " . number_format($total, 2, ',', '.');
        $mensagem .= "\n\nEndereço de entrega:\n";
        $mensagem .= "{$usuario['endereco']}, CEP: {$usuario['cep']}";

        mail(
            $usuario['email'],
            "Confirmação de Pedido",
            $mensagem,
            "From: pedidos@seudominio.com\r\nContent-Type: text/plain; charset=UTF-8"
        );

        unset($_SESSION['carrinho']);
        unset($_SESSION['cupom_aplicado']);
        header('Location: /carrinho/sucesso');
        exit;
    }
}
