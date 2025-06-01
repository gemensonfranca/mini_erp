<?php require_once __DIR__ . '/../templates/header.php'; ?>

<h1>Cadastro de Produto</h1>

<form method="post" action="/produto/salvar">
    <div class="mb-3">
        <label for="nome" class="form-label">Nome do Produto</label>
        <input type="text" id="nome" name="nome" class="form-control" required />
    </div>

    <div class="mb-3">
        <label for="preco" class="form-label">Preço (R$)</label>
        <input type="number" step="0.01" id="preco" name="preco" class="form-control" required />
    </div>

    <div class="mb-3">
        <label for="variacoes" class="form-label">Variações (formato: P (10), M (14), G (13))</label>
        <input type="text" id="variacoes" name="variacoes" class="form-control" />
        <div class="form-text">Informe no formato: Nome (quantidade), separados por vírgula</div>
    </div>

    <button type="submit" class="btn btn-primary">Cadastrar Produto</button>
</form>

<?php if (!empty($produtos)): ?>
    <hr>
    <h2>Produtos Cadastrados</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Preço (R$)</th>
                <th>Variações</th>
                <th>Quantidade em Estoque</th>
                <th>Quantidade para Comprar</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produtos as $prod): ?>
                <tr>
                    <td><?= htmlspecialchars($prod['nome']) ?></td>
                    <td><?= number_format($prod['preco'], 2, ',', '.') ?></td>
                    <td>
                        <form action="/carrinho/adicionar/<?= $prod['id'] ?>" method="post" style="display:inline-block;">
                            <?php foreach ($prod['variacoes'] as $var): ?>
                                <label class="form-check form-check-inline">
                                    <input type="radio" name="variacao_escolhida[<?= $prod['id'] ?>]" value="<?= $var['id'] ?>" class="form-check-input" required>
                                    <span class="form-check-label"><?= htmlspecialchars($var['nome']) ?></span>
                                </label>
                            <?php endforeach; ?>
                    </td>
                    <td><?= $prod['estoque_total'] ?></td>
                    <td>
                            <input type="number" name="quantidade" min="1" max="<?= $prod['estoque_total'] ?>" value="1" class="form-control" style="width: 80px; display:inline-block;">
                    </td>
                    <td>
                            <button type="submit" class="btn btn-sm btn-primary">Comprar</button>
                        </form>

                        <button class="btn btn-sm btn-warning"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEditar"
                            data-id="<?= $prod['id'] ?>"
                            data-nome="<?= htmlspecialchars($prod['nome']) ?>"
                            data-preco="<?= $prod['preco'] ?>"
                            data-variacoes="<?= implode(', ', array_column($prod['variacoes'], 'nome')) ?>"
                        >Editar</button>
                        <a href="/produto/excluir/<?= $prod['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja apagar este produto?')">Apagar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Nenhum produto cadastrado.</p>
<?php endif; ?>

<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" id="formEditar">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit-id">
                <div class="mb-3">
                    <label for="edit-nome" class="form-label">Nome</label>
                    <input type="text" name="nome" id="edit-nome" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="edit-preco" class="form-label">Preço</label>
                    <input type="number" step="0.01" name="preco" id="edit-preco" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="edit-variacoes" class="form-label">Variações</label>
                    <input type="text" name="variacoes" id="edit-variacoes" class="form-control">
                    <div class="form-text">Formato: P (10), M (5)</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar alterações</button>
            </div>
        </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modalEditar = document.getElementById('modalEditar');
    modalEditar.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        const id = button.getAttribute('data-id');
        const nome = button.getAttribute('data-nome');
        const preco = button.getAttribute('data-preco');
        const variacoes = button.getAttribute('data-variacoes');

        document.getElementById('edit-id').value = id;
        document.getElementById('edit-nome').value = nome;
        document.getElementById('edit-preco').value = preco;
        document.getElementById('edit-variacoes').value = variacoes;

        document.getElementById('formEditar').action = `/produto/editar/${id}`;
    });
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>