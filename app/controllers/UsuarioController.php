<?php

require_once '../core/Controller.php';

class UsuarioController extends Controller
{
    public function cadastrar()
    {
        $this->view('usuario/cadastrar');
    }

    public function salvar()
    {
        if (!isset($_POST['usuario'])) {
            echo "Dados do formulário ausentes.";
            exit;
        }

        $dados = $_POST['usuario'];

        if (
            empty($dados['nome']) ||
            empty($dados['email']) ||
            empty($dados['endereco']) ||
            empty($dados['cep'])
        ) {
            echo "Todos os campos são obrigatórios.";
            exit;
        }

        $_SESSION['usuario'] = [
            'nome'     => $dados['nome'],
            'email'    => $dados['email'],
            'endereco' => $dados['endereco'],
            'cep'      => $dados['cep']
        ];

        header('Location: /pedido/finalizar');
        exit;
    }
}
