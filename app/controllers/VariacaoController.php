<?php
require_once '../core/Controller.php';

class VariacaoController extends Controller {

    public function salvar() {
        $variacaoModel = $this->model('Variacao');
        $variacaoModel->salvar($_POST);
        header('Location: /produto');
    }
}
