<?php require_once __DIR__ . '/../templates/header.php'; ?>

<h1>Pedidos Realizados</h1>

<?php if (!empty($pedidos)): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Usuário</th>
                <th>CEP</th>
                <th>Total (R$)</th>
                <th>Frete (R$)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pedidos as $pedido): ?>
                <tr>
                    <td><?= htmlspecialchars($pedido['cliente_nome']) ?></td>
                    <td><?= htmlspecialchars($pedido['cep']) ?></td>
                    <td><?= number_format($pedido['total'], 2, ',', '.') ?></td>
                    <td><?= number_format($pedido['frete'], 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars($pedido['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Nenhum pedido encontrado.</p>
<?php endif; ?>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
