<?php require_once __DIR__ . '/../templates/header.php'; ?>

<h1>Gerenciar Cupons</h1>

<form method="post" action="/cupom/salvar">
    <div class="mb-3">
        <label for="codigo">Código</label>
        <input type="text" name="codigo" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="desconto">Desconto (%)</label>
        <input type="number" name="desconto" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="validade">Validade</label>
        <input type="date" name="validade" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="valor_minimo">Valor Mínimo do Pedido (R$)</label>
        <input type="number" name="valor_minimo" step="0.01" class="form-control">
    </div>
    <div class="mb-3">
        <label for="ativo">Ativo?</label>
        <select name="ativo" class="form-select">
            <option value="1">Sim</option>
            <option value="0">Não</option>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Cadastrar Cupom</button>
</form>

<?php if (!empty($cupons)): ?>
    <hr>
    <h2>Cupons Cadastrados</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Desconto (%)</th>
                <th>Validade</th>
                <th>Valor Mínimo</th>
                <th>Ativo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cupons as $cupom): ?>
                <tr>
                    <td><?= htmlspecialchars($cupom['codigo']) ?></td>
                    <td><?= $cupom['desconto'] ?>%</td>
                    <td><?= $cupom['validade'] ?></td>
                    <td>R$ <?= number_format($cupom['valor_minimo'], 2, ',', '.') ?></td>
                    <td><?= $cupom['ativo'] ? 'Sim' : 'Não' ?></td>
                    <td>
                        <button
                            class="btn btn-warning btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEditar"
                            data-id="<?= $cupom['id'] ?>"
                            data-codigo="<?= htmlspecialchars($cupom['codigo']) ?>"
                            data-desconto="<?= $cupom['desconto'] ?>"
                            data-validade="<?= $cupom['validade'] ?>"
                            data-valor-minimo="<?= $cupom['valor_minimo'] ?>"
                            data-ativo="<?= $cupom['ativo'] ?>"
                        >Editar</button>

                        <a href="/cupom/excluir/<?= $cupom['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Excluir este cupom?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" id="formEditar">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Cupom</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit-id">
                <div class="mb-3">
                    <label for="edit-codigo" class="form-label">Código</label>
                    <input type="text" name="codigo" id="edit-codigo" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="edit-desconto" class="form-label">Desconto (%)</label>
                    <input type="number" name="desconto" id="edit-desconto" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="edit-validade" class="form-label">Validade</label>
                    <input type="date" name="validade" id="edit-validade" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="edit-valor-minimo" class="form-label">Valor Mínimo</label>
                    <input type="number" step="0.01" name="valor_minimo" id="edit-valor-minimo" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="edit-ativo" class="form-label">Ativo?</label>
                    <select name="ativo" id="edit-ativo" class="form-select">
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
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
        const codigo = button.getAttribute('data-codigo');
        const desconto = button.getAttribute('data-desconto');
        const validade = button.getAttribute('data-validade');
        const valor_minimo = button.getAttribute('data-valor-minimo');
        const ativo = button.getAttribute('data-ativo');

        document.getElementById('edit-id').value = id;    
        document.getElementById('edit-codigo').value = codigo;
        document.getElementById('edit-desconto').value = desconto;
        document.getElementById('edit-validade').value = validade;
        document.getElementById('edit-valor-minimo').value = valor_minimo;
        document.getElementById('edit-ativo').value = ativo;

        document.getElementById('formEditar').action = `/cupom/editar/${id}`;
    });
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
