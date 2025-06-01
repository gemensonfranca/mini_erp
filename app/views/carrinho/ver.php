<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Meu Carrinho</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f8f8f8;
        }

        h2 {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .total {
            font-weight: bold;
        }

        .actions a {
            color: red;
            text-decoration: none;
            margin-left: 10px;
        }

        .link-voltar {
            display: inline-block;
            margin-top: 20px;
            background: #333;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
        }

        #resultado-frete {
            margin-top: 15px;
            font-weight: bold;
        }

        form#frete-form {
            margin-top: 20px;
        }

        form#frete-form input[type="text"] {
            padding: 5px;
            width: 200px;
        }

        form#frete-form button {
            padding: 6px 12px;
            margin-left: 10px;
            cursor: pointer;
        }

        .finalizar-pedido {
            display: inline-block;
            margin-top: 20px;
            background-color: green;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        form#cupom-form {
            margin-top: 20px;
        }

        form#cupom-form input[type="text"] {
            padding: 6px;
            width: 200px;
        }

        form#cupom-form button {
            padding: 6px 12px;
            margin-left: 10px;
            cursor: pointer;
        }

        .mensagem-erro {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<h2>Carrinho de Compras</h2>

<?php if (empty($itens)): ?>
    <p>Seu carrinho está vazio.</p>
<?php else: ?>

    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Preço</th>
                <th>Quantidade</th>
                <th>Subtotal</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($itens as $item): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8') ?>
                        <?php if (!empty($item['variacoes'])): ?>
                            (
                            <?php
                                $nomes = array_column($item['variacoes'], 'nome_variacao');
                                echo htmlspecialchars(implode(', ', $nomes), ENT_QUOTES, 'UTF-8');
                            ?>
                            )
                        <?php endif; ?>
                    </td>
                    <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                    <td><?= (int)$item['quantidade'] ?></td>
                    <td>R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></td>
                    <td class="actions">
                        <a href="/carrinho/remover/<?= urlencode($item['id']) ?>" onclick="return confirm('Remover item do carrinho?')">Remover</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><strong>Subtotal:</strong> R$ <?= number_format($subtotal, 2, ',', '.') ?></p>
    <p><strong>Frete:</strong> <?= ($frete == 0) ? 'Grátis' : 'R$ ' . number_format($frete, 2, ',', '.') ?></p>

    <!-- Cupom de desconto -->
    <form id="cupom-form" action="/carrinho/aplicarCupom" method="post">
        <label for="cupom">Cupom de Desconto:</label>
        <input type="text" id="cupom" name="cupom" placeholder="Digite o cupom" required />
        <button type="submit">Aplicar</button>
    </form>

    <?php if (!empty($_SESSION['erro'])): ?>
        <p class="mensagem-erro"><?= htmlspecialchars($_SESSION['erro']) ?></p>
        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <?php if (!empty($cupom_aplicado)): ?>
        <p><strong>Cupom aplicado:</strong> <?= htmlspecialchars($cupom_aplicado['codigo']) ?> - <?= $cupom_aplicado['desconto'] ?>% de desconto</p>
        <p><strong>Desconto:</strong> - R$ <?= number_format($desconto_aplicado, 2, ',', '.') ?></p>
    <?php endif; ?>

    <p class="total"><strong>Total:</strong> R$ <?= number_format($total, 2, ',', '.') ?></p>

    <p><a href="/carrinho/limpar" onclick="return confirm('Deseja limpar o carrinho?')">Limpar Carrinho</a></p>

    <form action="/pedido/finalizar" method="post">
        <button type="submit" class="finalizar-pedido">Finalizar Pedido</button>
    </form>

    <hr>

    <h3>Calcular prazo de entrega</h3>
    <form id="frete-form" onsubmit="return false;">
        <label for="cep">Digite seu CEP:</label><br>
        <input type="text" id="cep" name="cep" placeholder="Ex: 01001-000" maxlength="9" required />
        <button type="button" onclick="buscarFrete()">Calcular</button>
    </form>

    <div id="resultado-frete"></div>

<?php endif; ?>

<a href="/produto" class="link-voltar">← Continuar comprando</a>

<script>
function buscarFrete() {
    const cepInput = document.getElementById('cep');
    let cep = cepInput.value.replace(/\D/g, '');

    const resultado = document.getElementById('resultado-frete');
    resultado.innerText = '';

    if (cep.length !== 8) {
        resultado.innerText = 'CEP inválido. Digite um CEP com 8 números.';
        return;
    }

    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => {
            if (!response.ok) throw new Error('Erro na requisição');
            return response.json();
        })
        .then(data => {
            if (data.erro) {
                resultado.innerText = 'CEP não encontrado.';
                return;
            }

            const uf = data.uf;
            let prazoEntrega;

            switch (uf) {
                case 'SP':
                case 'RJ':
                case 'MG':
                    prazoEntrega = '2 a 4 dias úteis';
                    break;
                case 'PR':
                case 'SC':
                case 'RS':
                    prazoEntrega = '3 a 5 dias úteis';
                    break;
                case 'BA':
                case 'PE':
                case 'CE':
                    prazoEntrega = '4 a 7 dias úteis';
                    break;
                default:
                    prazoEntrega = '5 a 10 dias úteis';
            }

            resultado.innerText = `Destino: ${data.localidade} - ${uf}. Prazo estimado de entrega: ${prazoEntrega}.`;
        })
        .catch(() => {
            resultado.innerText = 'Erro ao consultar o CEP. Tente novamente mais tarde.';
        });
}
</script>

</body>
</html>
