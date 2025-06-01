<?php

require_once '../core/Controller.php';

class CupomController extends Controller
{
    public function index()
    {
        $cupomModel = $this->model('Cupom');
        $cupons = $cupomModel->listarTodos();

        require_once __DIR__ . '/../views/cupom/lista.php';
    }

    public function salvar()
    {
        $cupomModel = $this->model('Cupom');
        $cupomModel->salvar($_POST);
        header('Location: /cupom');
    }

    public function editar($id)
    {
        $cupomModel = $this->model('Cupom');
        $cupomModel->atualizar($id, $_POST);
        header('Location: /cupom');
    }

    public function excluir($id)
    {
        $cupomModel = $this->model('Cupom');
        $cupomModel->excluir($id);
        header('Location: /cupom');
    }
}
