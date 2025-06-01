<?php
require_once '../core/Controller.php';

class EstoqueController extends Controller {

    public function atualizar() {
        $estoqueModel = $this->model('Estoque');
        $estoqueModel->atualizarQuantidade($_POST['variacao_id'], $_POST['quantidade']);
        header('Location: /produto');
    }
}
